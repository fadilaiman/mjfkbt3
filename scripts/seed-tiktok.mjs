/**
 * TikTok one-time seed script.
 * Opens a visible browser so you can solve the CAPTCHA manually,
 * then automatically scrolls and collects all video URLs.
 *
 * Usage:
 *   node scripts/seed-tiktok.mjs
 *   node scripts/seed-tiktok.mjs https://www.tiktok.com/@mjfk.batu03/video/7614518858564816148
 */

import { chromium } from 'playwright';

const startUrl = process.argv[2] || 'https://www.tiktok.com/@mjfk.batu03';
const MAX_VIDEOS = 50;
const PROFILE_URL = 'https://www.tiktok.com/@mjfk.batu03';

const browser = await chromium.launch({
  headless: false,
  args: ['--start-maximized'],
});

const context = await browser.newContext({
  userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
  viewport: null, // use full window size
  locale: 'en-US',
});

const page = await context.newPage();

// If a video URL was given, go there first then navigate to profile
if (startUrl !== PROFILE_URL) {
  process.stderr.write(`\nOpening video: ${startUrl}\n`);
  await page.goto(startUrl, { waitUntil: 'domcontentloaded', timeout: 30000 });
  await page.waitForTimeout(2000);
  // Navigate to profile
  process.stderr.write('Navigating to profile page...\n');
  await page.goto(PROFILE_URL, { waitUntil: 'domcontentloaded', timeout: 30000 });
} else {
  await page.goto(PROFILE_URL, { waitUntil: 'domcontentloaded', timeout: 30000 });
}

process.stderr.write('\n');
process.stderr.write('============================================================\n');
process.stderr.write('  Browser is open. If a CAPTCHA appears, solve it manually.\n');
process.stderr.write('  Waiting for videos to appear (up to 3 minutes)...\n');
process.stderr.write('============================================================\n\n');

// Wait until the video grid is visible (CAPTCHA solved + videos loaded)
try {
  await page.waitForFunction(
    () => document.querySelectorAll('a[href*="/video/"]').length > 0,
    { timeout: 180_000, polling: 1000 }
  );
} catch {
  process.stderr.write('Timed out waiting for videos. Exiting.\n');
  await browser.close();
  process.stdout.write(JSON.stringify([]));
  process.exit(1);
}

process.stderr.write('Videos detected! Scrolling to load more...\n');

// Scroll down repeatedly to lazy-load all videos
let prevCount = 0;
let sameCount = 0;

for (let i = 0; i < 15; i++) {
  await page.evaluate(() => window.scrollBy(0, window.innerHeight * 2));
  await page.waitForTimeout(1500);

  const count = await page.evaluate(
    () => document.querySelectorAll('a[href*="/video/"]').length
  );

  process.stderr.write(`  Scroll ${i + 1}: ${count} video links found\n`);

  if (count >= MAX_VIDEOS) break;

  if (count === prevCount) {
    sameCount++;
    if (sameCount >= 3) break; // no new videos after 3 scrolls
  } else {
    sameCount = 0;
  }
  prevCount = count;
}

// Extract all unique video URLs
const urls = await page.evaluate((max) => {
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
}, MAX_VIDEOS);

process.stderr.write(`\nDone! Found ${urls.length} unique video URLs.\n`);
process.stderr.write('Closing browser...\n\n');

await browser.close();
process.stdout.write(JSON.stringify(urls));
