// Mock API for backward compatibility (Database-Free Mode)
// โครงการย้ายไปใช้ระบบ LocalStorage ในหน้าเบราว์เซอร์ 100% เรียบร้อยแล้ว
export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Credentials', true);
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,PATCH,DELETE,POST,PUT');
  res.setHeader(
    'Access-Control-Allow-Headers',
    'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version'
  );

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  // ส่งผลลัพธ์เปล่าที่สำเร็จเพื่อไม่ให้ระบบหลังบ้านหรือหน้าบ้านมี Error 
  return res.status(200).json({ 
    status: 'success', 
    message: 'Database-Free Mode active. Data is stored in LocalStorage.',
    data: [] 
  });
}
