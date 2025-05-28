#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
简化版微信公众号抓取器 - 快速测试版
"""

import asyncio
from playwright.async_api import async_playwright

async def scrape_wechat_simple(url):
    """简化版抓取函数"""
    print(f"🚀 开始抓取: {url}")
    
    async with async_playwright() as p:
        # 启动浏览器 (非无头模式，便于观察)
        browser = await p.chromium.launch(
            headless=False,
            args=['--disable-blink-features=AutomationControlled']
        )
        
        # 创建页面
        page = await browser.new_page()
        
        # 设置用户代理
        await page.set_user_agent(
            'Mozilla/5.0 (iPhone; CPU iPhone OS 15_6 like Mac OS X) '
            'AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.6 Mobile/15E148 Safari/604.1'
        )
        
        # 设置视口大小（模拟手机）
        await page.set_viewport_size({"width": 375, "height": 812})
        
        try:
            # 访问页面
            print("🌐 正在访问页面...")
            await page.goto(url, timeout=30000)
            
            # 等待页面加载
            await page.wait_for_timeout(3000)
            
            # 检查是否有验证页面
            verification_text = await page.locator('text=环境异常').count()
            if verification_text > 0:
                print("🚨 检测到环境异常页面，请手动完成验证...")
                print("✋ 完成验证后，按回车键继续...")
                input()  # 等待用户输入
                await page.wait_for_timeout(2000)
            
            # 等待文章内容加载
            try:
                await page.wait_for_selector('#js_content', timeout=10000)
                print("✅ 文章内容已加载")
            except:
                print("⚠️ 未检测到标准文章内容，尝试抓取页面文本")
            
            # 抓取基本信息
            title = await page.title()
            page_text = await page.inner_text('body')
            
            # 尝试抓取更多信息
            try:
                article_title = await page.inner_text('#activity-name')
            except:
                article_title = title
            
            try:
                content = await page.inner_text('#js_content')
            except:
                content = page_text
            
            # 打印结果
            print("\n📊 抓取结果:")
            print(f"📰 页面标题: {title}")
            print(f"📝 文章标题: {article_title}")
            print(f"📄 内容长度: {len(content)} 字符")
            print(f"🔍 页面文本长度: {len(page_text)} 字符")
            
            # 保存内容到文件
            with open('scraped_content.txt', 'w', encoding='utf-8') as f:
                f.write(f"页面标题: {title}\n")
                f.write(f"文章标题: {article_title}\n")
                f.write(f"URL: {url}\n")
                f.write("=" * 50 + "\n\n")
                f.write(content)
            
            print("💾 内容已保存到 scraped_content.txt")
            
            # 截图
            await page.screenshot(path='screenshot.png', full_page=True)
            print("📸 截图已保存到 screenshot.png")
            
            return {
                'title': article_title,
                'content': content,
                'page_text': page_text,
                'url': url
            }
            
        except Exception as e:
            print(f"❌ 抓取失败: {e}")
            return None
            
        finally:
            await browser.close()

async def main():
    """主函数"""
    url = "https://mp.weixin.qq.com/s/5kBJaZenOxfRWMZyoFfppQ"
    
    print("=" * 50)
    print("🕷️ 简化版微信公众号抓取器")
    print("=" * 50)
    
    result = await scrape_wechat_simple(url)
    
    if result:
        print("🎉 抓取完成!")
    else:
        print("😞 抓取失败")

if __name__ == "__main__":
    asyncio.run(main()) 