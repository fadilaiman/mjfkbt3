/**
 * TikTok profile scraper using Playwright + stealth plugin.
 * Usage: node scripts/scrape-tiktok.mjs <profileUrl> [maxVideos]
 * Output: JSON array of video URLs to stdout, debug to stderr.
 */

import { chromium } from 'playwright-extra';
import StealthPlugin from 'puppeteer-extra-plugin-stealth';

chromium.use(StealthPlugin());

const profileUrl = process.argv[2] || 'https://www.tiktok.com/@mjfk.batu03';
const maxVideos  = parseInt(process.argv[3] || '20', 10);

(async () => {
  const browser = await chromium.launch({
    headless: true,
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-blink-features=AutomationControlled',
    ],
  });

  const context = await browser.newContext({
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    viewport: { width: 1280, height: 800 },
    locale: 'en-US',
    timezoneId: 'Asia/Kuala_Lumpur',
    extraHTTPHeaders: {
      'Accept-Language': 'en-US,en;q=0.9',
    },
  });

  // Hide webdriver property
  await context.addInitScript(() => {
    Object.defineProperty(navigator, 'webdriver', { get: () => false });
    Object.defineProperty(navigator, 'plugins', { get: () => [1, 2, 3] });
    window.chrome = { runtime: {} };
  });

  const page = await context.newPage();
  page.on('console', () => {}); // silence page console

  try {
    await page.goto(profileUrl, { waitUntil: 'domcontentloaded', timeout: 30000 });

    // Wait and scroll to trigger lazy-load
    await page.waitForTimeout(4000);
    await page.evaluate(() => window.scrollBy(0, 600));
    await page.waitForTimeout(2000);

    // Dismiss any CAPTCHA/modal by pressing Escape
    await page.keyboard.press('Escape');
    await page.waitForTimeout(1000);

    const urls = await page.evaluate((max) => {
      // Strategy 1: data-e2e attribute
      const byAttr = document.querySelectorAll('[data-e2e="user-post-item"] a[href*="/video/"]');
      if (byAttr.length > 0) {
        return [...new Set(Array.from(byAttr).map(a => a.href.split('?')[0]))].slice(0, max);
      }

      // Strategy 2: any anchor linking to a /video/ path with long numeric ID
      const seen = new Set();
      const results = [];
      for (const a of document.querySelectorAll('a[href*="/video/"]')) {
        const href = a.href.split('?')[0];
        if (!seen.has(href) && /\/video\/\d{15,}/.test(href)) {
          seen.add(href);
          results.push(href);
          if (results.length >= max) break;
        }
      }
      return results;
    }, maxVideos);

    process.stdout.write(JSON.stringify(urls));
    process.exitCode = 0;
  } catch (err) {
    process.stderr.write('Error: ' + err.message + '\n');
    process.stdout.write(JSON.stringify([]));
    process.exitCode = 1;
  } finally {
    await browser.close();
  }
})();
