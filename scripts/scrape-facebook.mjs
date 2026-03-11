/**
 * Facebook page scraper using Playwright (headed — visible browser).
 * Opens facebook.com/PAGE in a real browser window so the user can dismiss
 * any login popup or CAPTCHA, then auto-scrapes post permalink URLs.
 *
 * Usage:
 *   node scripts/scrape-facebook.mjs <pageUrl> <waitSeconds> <maxPosts> <outputFile>
 *
 * Output: JSON array written to <outputFile>.
 * Progress messages go to stdout (shown in terminal via passthru).
 */

import { chromium } from 'playwright-extra';
import StealthPlugin from 'puppeteer-extra-plugin-stealth';
import { writeFileSync } from 'fs';

chromium.use(StealthPlugin());

const pageUrl    = process.argv[2] || 'https://www.facebook.com/mjfkbt3';
const waitSecs   = parseInt(process.argv[3] || '25', 10);
const maxPosts   = parseInt(process.argv[4] || '40', 10);
const outputFile = process.argv[5] || '/tmp/fb_posts.json';

function log(msg) {
  process.stdout.write(msg + '\n');
}

(async () => {
  const browser = await chromium.launch({
    headless: false,
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-blink-features=AutomationControlled',
      '--start-maximized',
    ],
  });

  const context = await browser.newContext({
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
    viewport: null,
    locale: 'ms-MY',
    timezoneId: 'Asia/Kuala_Lumpur',
    extraHTTPHeaders: { 'Accept-Language': 'ms-MY,ms;q=0.9,en;q=0.8' },
  });

  await context.addInitScript(() => {
    Object.defineProperty(navigator, 'webdriver', { get: () => false });
    window.chrome = { runtime: {} };
  });

  const page = await context.newPage();

  try {
    log(`Navigating to ${pageUrl} ...`);
    await page.goto(pageUrl, { waitUntil: 'domcontentloaded', timeout: 30000 });
    await page.waitForTimeout(3000);

    // Try to auto-close login popup / cookie banner
    const closeSelectors = [
      '[aria-label="Close"]',
      '[data-testid="cookie-policy-dialog-accept-button"]',
      'div[role="dialog"] [aria-label="Close"]',
      'div[role="dialog"] svg[viewBox] >> ..',
    ];
    for (const sel of closeSelectors) {
      try {
        const btn = page.locator(sel).first();
        if (await btn.isVisible({ timeout: 1500 })) {
          await btn.click();
          log('  Dismissed popup.');
          await page.waitForTimeout(1000);
          break;
        }
      } catch (_) {}
    }

    log('');
    log('=======================================================');
    log(' Browser is open. If you see a login wall or CAPTCHA,');
    log(' please handle it now. Scraping starts automatically.');
    log('=======================================================');

    // Countdown
    for (let i = waitSecs; i > 0; i--) {
      process.stdout.write(`\r  Starting in ${String(i).padStart(2, ' ')}s — interact with the browser now...  `);
      await page.waitForTimeout(1000);
    }
    log('\n');
    log('Scrolling to load posts...');

    // Scroll to trigger lazy-loading
    for (let s = 0; s < 6; s++) {
      await page.evaluate(() => window.scrollBy(0, window.innerHeight * 2));
      await page.waitForTimeout(2500);
      log(`  Scroll ${s + 1}/6`);
    }

    // Scroll back to top then back down (sometimes loads more)
    await page.evaluate(() => window.scrollTo(0, 0));
    await page.waitForTimeout(1000);
    for (let s = 0; s < 3; s++) {
      await page.evaluate(() => window.scrollBy(0, window.innerHeight * 3));
      await page.waitForTimeout(2000);
    }

    // Screenshot for debugging
    const screenshotPath = outputFile + '.png';
    await page.screenshot({ path: screenshotPath, fullPage: false });
    log(`Screenshot saved: ${screenshotPath}`);

    // Dump all unique href patterns to help diagnose selector issues
    const allHrefs = await page.evaluate(() => {
      const hrefs = new Set();
      for (const a of document.querySelectorAll('a[href]')) {
        try {
          const u = new URL(a.href);
          if (/facebook\.com/.test(u.hostname)) {
            // Keep only the pathname pattern
            hrefs.add(u.pathname.replace(/\/[0-9a-zA-Z_-]{10,}/, '/<id>').slice(0, 80));
          }
        } catch (_) {}
      }
      return [...hrefs].slice(0, 40);
    });
    log('Sample href patterns found on page:');
    allHrefs.forEach(h => log('  ' + h));

    log('\nExtracting post URLs...');

    const urls = await page.evaluate((max) => {
      const seen   = new Set();
      const result = [];

      for (const a of document.querySelectorAll('a[href]')) {
        let href = '';
        try {
          const u = new URL(a.href);
          if (! /facebook\.com/.test(u.hostname)) continue;

          const path     = u.pathname;
          const fbid     = u.searchParams.get('fbid');
          const storyFbid = u.searchParams.get('story_fbid');

          // Accept multiple Facebook post URL formats (modern + legacy)
          const isPost = (
            /\/posts\//.test(path) ||                                   // /PAGE/posts/pfbid0...
            /\/photos\//.test(path) ||                                  // /PAGE/photos/123
            (path === '/photo' || path === '/photo/') && fbid ||        // /photo?fbid=123
            (path === '/permalink.php' && storyFbid) ||                 // /permalink.php?story_fbid=
            storyFbid                                                   // any URL with story_fbid
          );
          if (! isPost) continue;

          // Skip videos and reels
          if (/\/(videos|reels|reel|video)\//.test(path)) continue;
          if (u.searchParams.has('video_id')) continue;

          // Build clean canonical URL
          if ((path === '/photo' || path === '/photo/') && fbid) {
            href = `https://www.facebook.com/photo?fbid=${fbid}`;
          } else if (storyFbid) {
            const p = new URLSearchParams({ story_fbid: storyFbid });
            if (u.searchParams.has('id')) p.set('id', u.searchParams.get('id'));
            href = `https://www.facebook.com/permalink.php?${p}`;
          } else {
            href = u.origin + path;
          }
        } catch (_) {
          continue;
        }

        if (! seen.has(href)) {
          seen.add(href);
          result.push(href);
          if (result.length >= max) break;
        }
      }

      return result;
    }, maxPosts);

    log(`Found ${urls.length} post URL(s).`);

    writeFileSync(outputFile, JSON.stringify(urls));
    log(`Results written to: ${outputFile}`);
    process.exitCode = 0;

  } catch (err) {
    log('ERROR: ' + err.message);
    writeFileSync(outputFile, JSON.stringify([]));
    process.exitCode = 1;
  } finally {
    await browser.close();
  }
})();
