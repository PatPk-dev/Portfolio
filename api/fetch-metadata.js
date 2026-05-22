export default async function handler(req, res) {
  // CORS Headers
  res.setHeader('Access-Control-Allow-Credentials', true);
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,PATCH,DELETE,POST,PUT');
  res.setHeader(
    'Access-Control-Allow-Headers',
    'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version'
  );

  // Handle Options preflight request
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ status: 'error', message: 'Method not allowed' });
  }

  const { url } = req.body;

  if (!url) {
    return res.status(400).json({ status: 'error', message: 'กรุณาระบุ URL ที่ต้องการดึงข้อมูล' });
  }

  try {
    // Validate URL syntax
    new URL(url);
  } catch (e) {
    return res.status(400).json({ status: 'error', message: 'รูปแบบ URL ไม่ถูกต้อง' });
  }

  try {
    // Fetch HTML with User-Agent to prevent bot blocking
    const response = await fetch(url, {
      headers: {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language': 'en-US,en;q=0.5',
      },
      signal: AbortSignal.timeout(10000), // 10 seconds timeout
    });

    if (!response.ok) {
      return res.status(400).json({
        status: 'error',
        message: `ไม่สามารถเข้าถึงเว็บไซต์ได้ (HTTP Status ${response.status})`
      });
    }

    const html = await response.text();

    // Helper functions for metadata extraction using Regex (Zero dependencies)
    const getMetaProperty = (property) => {
      // Handles both double and single quotes, and property before or after content
      const regex1 = new RegExp(`<meta[^>]*property=["']${property}["'][^>]*content=["']([^"']*)["'][^>]*>`, 'i');
      const match1 = html.match(regex1);
      if (match1) return decodeHtmlEntities(match1[1]);

      const regex2 = new RegExp(`<meta[^>]*content=["']([^"']*)["'][^>]*property=["']${property}["'][^>]*>`, 'i');
      const match2 = html.match(regex2);
      if (match2) return decodeHtmlEntities(match2[1]);

      // Fallback for name attribute (some sites use name instead of property)
      const regexName1 = new RegExp(`<meta[^>]*name=["']${property}["'][^>]*content=["']([^"']*)["'][^>]*>`, 'i');
      const matchName1 = html.match(regexName1);
      if (matchName1) return decodeHtmlEntities(matchName1[1]);

      const regexName2 = new RegExp(`<meta[^>]*content=["']([^"']*)["'][^>]*name=["']${property}["'][^>]*>`, 'i');
      const matchName2 = html.match(regexName2);
      return matchName2 ? decodeHtmlEntities(matchName2[1]) : null;
    };

    // Helper to decode HTML Entities
    function decodeHtmlEntities(str) {
      if (!str) return '';
      return str
        .replace(/&quot;/g, '"')
        .replace(/&apos;/g, "'")
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&amp;/g, '&')
        .replace(/&#(\d+);/g, (match, dec) => String.fromCharCode(dec))
        .replace(/&#x([0-9a-f]+);/gi, (match, hex) => String.fromCharCode(parseInt(hex, 16)))
        .trim();
    }

    // 1. Title Extraction
    let title = getMetaProperty('og:title');
    if (!title) {
      const titleMatch = html.match(/<title[^>]*>([^<]*)<\/title>/i);
      title = titleMatch ? decodeHtmlEntities(titleMatch[1]) : null;
    }
    title = title ? title.trim() : new URL(url).hostname;

    // 2. Description Extraction
    let description = getMetaProperty('og:description') || getMetaProperty('description');
    if (!description) {
      const descMatch = html.match(/<meta[^>]*name=["']description["'][^>]*content=["']([^"']*)["'][^>]*>/i);
      description = descMatch ? decodeHtmlEntities(descMatch[1]) : null;
    }
    description = description ? description.trim() : 'ไม่มีคำอธิบายสำหรับลิงก์นี้';

    // 3. Image URL Extraction
    let imageUrl = getMetaProperty('og:image');
    if (imageUrl) {
      imageUrl = imageUrl.trim();
      
      // Resolve relative path to absolute
      if (imageUrl && !/^https?:\/\//i.test(imageUrl)) {
        try {
          const parsed = new URL(url);
          const baseUrl = `${parsed.protocol}//${parsed.host}`;
          if (imageUrl.startsWith('/')) {
            imageUrl = `${baseUrl}${imageUrl}`;
          } else {
            const pathParts = parsed.pathname.split('/');
            pathParts.pop(); // Remove current file
            const dirPath = pathParts.join('/');
            imageUrl = `${baseUrl}${dirPath}/${imageUrl}`;
          }
        } catch (_) {
          // Keep relative if parsing fails
        }
      }
    }

    return res.status(200).json({
      status: 'success',
      data: {
        original_url: url,
        title: title,
        description: description,
        image_url: imageUrl || '',
      }
    });

  } catch (error) {
    console.error('Error fetching metadata:', error);
    return res.status(500).json({
      status: 'error',
      message: `เกิดข้อผิดพลาดในการดึงข้อมูล: ${error.message}`
    });
  }
}
