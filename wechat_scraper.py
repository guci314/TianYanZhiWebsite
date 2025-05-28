#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
微信公众号文章抓取器
使用 Playwright 绕过反爬虫机制抓取微信公众号文章内容
"""

import asyncio
import json
import random
import time
from datetime import datetime
from pathlib import Path
from playwright.async_api import async_playwright, TimeoutError as PlaywrightTimeoutError


class WeChatScraper:
    def __init__(self):
        self.max_retries = 3
        self.delay_range = (2, 5)
        self.user_agents = [
            'Mozilla/5.0 (iPhone; CPU iPhone OS 15_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.6 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.2 Mobile/15E148 Safari/604.1'
        ]
    
    async def setup_browser(self):
        """设置浏览器实例"""
        browser = await self.playwright.chromium.launch(
            headless=False,  # 设置为 False 以便观察和手动处理验证
            args=[
                '--no-first-run',
                '--disable-blink-features=AutomationControlled',
                '--disable-web-security',
                '--disable-features=VizDisplayCompositor',
                '--disable-dev-shm-usage',
                '--no-sandbox',
                '--disable-gpu',
                '--disable-extensions',
                '--disable-plugins',
                '--disable-images',  # 禁用图片加载以加快速度
            ]
        )
        
        # 创建浏览器上下文，模拟iPhone
        context = await browser.new_context(
            user_agent=random.choice(self.user_agents),
            viewport={'width': 375, 'height': 812},
            device_scale_factor=2,
            is_mobile=True,
            has_touch=True,
            extra_http_headers={
                'Accept-Language': 'zh-CN,zh;q=0.9,en;q=0.8',
                'Accept-Encoding': 'gzip, deflate, br',
                'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache',
                'Sec-Fetch-Dest': 'document',
                'Sec-Fetch-Mode': 'navigate',
                'Sec-Fetch-Site': 'none',
                'Upgrade-Insecure-Requests': '1'
            }
        )
        
        page = await context.new_page()
        
        # 注入反检测脚本
        await page.add_init_script("""
            // 移除 webdriver 属性
            Object.defineProperty(navigator, 'webdriver', {
                get: () => undefined,
            });
            
            // 修改 plugins
            Object.defineProperty(navigator, 'plugins', {
                get: () => [1, 2, 3, 4, 5],
            });
            
            // 修改语言
            Object.defineProperty(navigator, 'languages', {
                get: () => ['zh-CN', 'zh', 'en'],
            });
            
            // 隐藏自动化特征
            Object.defineProperty(navigator, 'permissions', {
                get: () => ({
                    query: () => Promise.resolve({ state: 'granted' }),
                }),
            });
            
            // 模拟正常的屏幕属性
            Object.defineProperty(screen, 'availHeight', { get: () => 812 });
            Object.defineProperty(screen, 'availWidth', { get: () => 375 });
        """)
        
        return browser, page
    
    async def simulate_human_behavior(self, page):
        """模拟人类行为"""
        # 随机等待
        await asyncio.sleep(random.uniform(*self.delay_range))
        
        # 模拟触摸滑动
        await page.evaluate("""
            () => {
                window.scrollBy(0, Math.random() * 200);
            }
        """)
        
        await asyncio.sleep(random.uniform(0.5, 1.5))
    
    async def handle_verification(self, page):
        """处理验证页面"""
        try:
            # 检查是否出现验证页面
            verification_selectors = [
                'text=环境异常',
                'text=完成验证后即可继续访问',
                'text=去验证',
                '.verify-wrap',
                '#verify_container'
            ]
            
            for selector in verification_selectors:
                if await page.locator(selector).count() > 0:
                    print("🚨 检测到环境异常验证页面")
                    print("📱 请在浏览器中手动完成验证...")
                    print("⏳ 脚本将等待验证完成...")
                    
                    # 等待验证完成，检查页面是否跳转到正常内容
                    verification_completed = False
                    wait_time = 0
                    max_wait = 120  # 最多等待2分钟
                    
                    while not verification_completed and wait_time < max_wait:
                        await asyncio.sleep(5)
                        wait_time += 5
                        
                        # 检查是否已经跳转到正常页面
                        if await page.locator('#js_content').count() > 0:
                            verification_completed = True
                            print("✅ 验证完成，继续抓取...")
                        elif await page.locator('text=环境异常').count() == 0:
                            verification_completed = True
                            print("✅ 页面已更新，继续抓取...")
                        else:
                            print(f"⏳ 等待验证中... ({wait_time}s)")
                    
                    if not verification_completed:
                        print("❌ 验证超时，请手动访问链接")
                        return False
                    
                    break
            
            return True
            
        except Exception as e:
            print(f"❌ 处理验证时出错: {e}")
            return False
    
    async def extract_article_content(self, page):
        """提取文章内容"""
        try:
            # 等待页面内容加载
            await page.wait_for_selector('body', timeout=10000)
            
            # 先处理可能的验证
            if not await self.handle_verification(page):
                return None
            
            # 尝试等待文章内容加载
            content_selectors = ['#js_content', '.rich_media_content', 'article', '.article-content']
            content_loaded = False
            
            for selector in content_selectors:
                try:
                    await page.wait_for_selector(selector, timeout=5000)
                    content_loaded = True
                    break
                except PlaywrightTimeoutError:
                    continue
            
            if not content_loaded:
                print("⚠️ 未能加载到文章内容，尝试抓取页面可见文本")
            
            # 抓取文章数据
            article_data = await page.evaluate("""
                () => {
                    // 尝试多种选择器获取标题
                    const getTitleElement = () => {
                        const selectors = [
                            '#activity-name',
                            '.rich_media_title',
                            'h1',
                            'h2',
                            '.title',
                            '[data-role="title"]'
                        ];
                        for (let selector of selectors) {
                            const element = document.querySelector(selector);
                            if (element && element.innerText.trim()) {
                                return element;
                            }
                        }
                        return null;
                    };
                    
                    // 尝试多种选择器获取内容
                    const getContentElement = () => {
                        const selectors = [
                            '#js_content',
                            '.rich_media_content',
                            'article',
                            '.article-content',
                            '.content',
                            'main'
                        ];
                        for (let selector of selectors) {
                            const element = document.querySelector(selector);
                            if (element) {
                                return element;
                            }
                        }
                        return document.body;
                    };
                    
                    const titleElement = getTitleElement();
                    const contentElement = getContentElement();
                    
                    // 获取作者信息
                    const getAuthor = () => {
                        const authorSelectors = [
                            '#js_name',
                            '.rich_media_meta_nickname',
                            '.author',
                            '[data-role="author"]'
                        ];
                        for (let selector of authorSelectors) {
                            const element = document.querySelector(selector);
                            if (element && element.innerText.trim()) {
                                return element.innerText.trim();
                            }
                        }
                        return '';
                    };
                    
                    // 获取发布时间
                    const getPublishTime = () => {
                        const timeSelectors = [
                            '#publish_time',
                            '.rich_media_meta_text',
                            '.publish-time',
                            '[data-role="publish-time"]'
                        ];
                        for (let selector of timeSelectors) {
                            const element = document.querySelector(selector);
                            if (element && element.innerText.trim()) {
                                return element.innerText.trim();
                            }
                        }
                        return '';
                    };
                    
                    return {
                        url: window.location.href,
                        title: titleElement ? titleElement.innerText.trim() : document.title || '',
                        content: contentElement ? contentElement.innerText.trim() : '',
                        html_content: contentElement ? contentElement.innerHTML : '',
                        author: getAuthor(),
                        publish_time: getPublishTime(),
                        page_text: document.body.innerText.trim(),
                        images: Array.from(document.querySelectorAll('img')).map(img => ({
                            src: img.src,
                            alt: img.alt || '',
                            title: img.title || ''
                        })).filter(img => img.src && !img.src.includes('data:')),
                        links: Array.from(document.querySelectorAll('a')).map(a => ({
                            text: a.innerText.trim(),
                            href: a.href,
                            title: a.title || ''
                        })).filter(link => link.href && !link.href.startsWith('javascript:')),
                        scraped_at: new Date().toISOString()
                    };
                }
            """)
            
            return article_data
            
        except Exception as e:
            print(f"❌ 提取内容时出错: {e}")
            return None
    
    async def scrape_article(self, url):
        """抓取文章主函数"""
        print(f"🚀 开始抓取: {url}")
        
        for attempt in range(self.max_retries):
            try:
                print(f"📝 尝试第 {attempt + 1} 次...")
                
                browser, page = await self.setup_browser()
                
                try:
                    # 访问页面
                    print("🌐 正在访问页面...")
                    await page.goto(url, wait_until='domcontentloaded', timeout=30000)
                    
                    # 模拟人类行为
                    await self.simulate_human_behavior(page)
                    
                    # 提取内容
                    print("📖 正在提取内容...")
                    article_data = await self.extract_article_content(page)
                    
                    if article_data:
                        print("✅ 抓取成功!")
                        return article_data
                    else:
                        print("⚠️ 未能提取到有效内容")
                        
                except PlaywrightTimeoutError:
                    print("⏰ 页面加载超时")
                except Exception as e:
                    print(f"❌ 抓取过程中出错: {e}")
                
                finally:
                    await browser.close()
                
                if attempt < self.max_retries - 1:
                    wait_time = random.uniform(5, 10)
                    print(f"⏳ 等待 {wait_time:.1f} 秒后重试...")
                    await asyncio.sleep(wait_time)
                    
            except Exception as e:
                print(f"❌ 第 {attempt + 1} 次尝试失败: {e}")
        
        print("❌ 所有尝试都失败了")
        return None
    
    async def save_result(self, data, output_dir="output"):
        """保存抓取结果"""
        if not data:
            print("❌ 没有数据可保存")
            return
        
        # 创建输出目录
        output_path = Path(output_dir)
        output_path.mkdir(exist_ok=True)
        
        # 生成文件名
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        title_clean = "".join(c for c in data.get('title', 'untitled')[:30] if c.isalnum() or c in (' ', '-', '_')).strip()
        
        # 保存 JSON 文件
        json_file = output_path / f"{timestamp}_{title_clean}.json"
        with open(json_file, 'w', encoding='utf-8') as f:
            json.dump(data, f, ensure_ascii=False, indent=2)
        print(f"💾 JSON 已保存: {json_file}")
        
        # 保存纯文本文件
        txt_file = output_path / f"{timestamp}_{title_clean}.txt"
        with open(txt_file, 'w', encoding='utf-8') as f:
            f.write(f"标题: {data.get('title', '无标题')}\n")
            f.write(f"作者: {data.get('author', '未知')}\n")
            f.write(f"发布时间: {data.get('publish_time', '未知')}\n")
            f.write(f"抓取时间: {data.get('scraped_at', '未知')}\n")
            f.write(f"链接: {data.get('url', '未知')}\n")
            f.write("=" * 50 + "\n\n")
            f.write(data.get('content', '无内容'))
        print(f"📄 文本已保存: {txt_file}")
        
        # 保存 HTML 文件
        html_file = output_path / f"{timestamp}_{title_clean}.html"
        with open(html_file, 'w', encoding='utf-8') as f:
            f.write(f"""
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{data.get('title', '无标题')}</title>
    <style>
        body {{ font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 20px; }}
        .meta {{ color: #666; font-size: 14px; margin-bottom: 20px; }}
        .content {{ margin-top: 20px; }}
    </style>
</head>
<body>
    <h1>{data.get('title', '无标题')}</h1>
    <div class="meta">
        <p>作者: {data.get('author', '未知')}</p>
        <p>发布时间: {data.get('publish_time', '未知')}</p>
        <p>抓取时间: {data.get('scraped_at', '未知')}</p>
        <p>原文链接: <a href="{data.get('url', '#')}" target="_blank">{data.get('url', '未知')}</a></p>
    </div>
    <div class="content">
        {data.get('html_content', data.get('content', '无内容'))}
    </div>
</body>
</html>
            """)
        print(f"🌐 HTML 已保存: {html_file}")
    
    async def run(self, url):
        """运行抓取器"""
        self.playwright = await async_playwright().start()
        
        try:
            data = await self.scrape_article(url)
            await self.save_result(data)
            
            if data:
                print("\n📊 抓取摘要:")
                print(f"📰 标题: {data.get('title', '无标题')}")
                print(f"👤 作者: {data.get('author', '未知')}")
                print(f"📅 发布时间: {data.get('publish_time', '未知')}")
                print(f"📝 内容长度: {len(data.get('content', ''))} 字符")
                print(f"🖼️ 图片数量: {len(data.get('images', []))}")
                print(f"🔗 链接数量: {len(data.get('links', []))}")
            
            return data
            
        finally:
            await self.playwright.stop()


async def main():
    """主函数"""
    # 目标URL
    url = "https://mp.weixin.qq.com/s/5kBJaZenOxfRWMZyoFfppQ"
    
    # 创建抓取器实例
    scraper = WeChatScraper()
    
    # 运行抓取
    try:
        result = await scraper.run(url)
        if result:
            print("🎉 抓取完成!")
        else:
            print("😞 抓取失败")
    except KeyboardInterrupt:
        print("\n👋 用户中断抓取")
    except Exception as e:
        print(f"❌ 程序错误: {e}")


if __name__ == "__main__":
    print("=" * 60)
    print("🕷️  微信公众号文章抓取器")
    print("=" * 60)
    
    # 运行异步主函数
    asyncio.run(main()) 