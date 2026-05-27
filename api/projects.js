import mysql from 'mysql2/promise';
import fs from 'fs';
import path from 'path';

// โหลดตัวแปรสภาพแวดล้อมจากไฟล์ .env แบบแมนนวลกรณีรันแบบธรรมดา
if (process.env.NODE_ENV !== 'production') {
  try {
    const envPath = path.resolve(process.cwd(), '.env');
    if (fs.existsSync(envPath)) {
      const envContent = fs.readFileSync(envPath, 'utf-8');
      envContent.split('\n').forEach(line => {
        const match = line.match(/^\s*([\w.-]+)\s*=\s*(.*)?\s*$/);
        if (match) {
          const key = match[1];
          let value = (match[2] || '').trim();
          if (value.startsWith('"') && value.endsWith('"')) {
            value = value.substring(1, value.length - 1);
          }
          if (!process.env[key]) {
            process.env[key] = value;
          }
        }
      });
    }
  } catch (e) {
    console.warn('Failed to load manual .env:', e);
  }
}

// สร้าง Connection Pool สำหรับเชื่อมต่อ MySQL
let pool;

function getDbPool() {
  if (!pool) {
    pool = mysql.createPool({
      host: process.env.DB_HOST || '127.0.0.1',
      port: parseInt(process.env.DB_PORT || '3306'),
      user: process.env.DB_USER || 'root',
      password: process.env.DB_PASSWORD || '',
      database: process.env.DB_NAME || 'portfolio',
      connectionLimit: 10,
      waitForConnections: true,
      queueLimit: 0,
      connectTimeout: 5000 // 5 seconds connection timeout
    });
  }
  return pool;
}

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

  try {
    const db = getDbPool();

    // 1. ดึงข้อมูลโครงการทั้งหมด (GET)
    if (req.method === 'GET') {
      try {
        const [rows] = await db.query('SELECT * FROM projects ORDER BY created_at DESC');
        return res.status(200).json({ status: 'success', data: rows });
      } catch (dbError) {
        console.error('MySQL query error:', dbError);
        return res.status(500).json({
          status: 'error',
          type: 'db_connection_error',
          message: `ไม่สามารถเชื่อมต่อฐานข้อมูล MySQL ได้: ${dbError.message}. กรุณาเปิดโปรแกรม XAMPP และกดปุ่ม Start บนหน้า MySQL หรือนำเข้าไฟล์ database.sql เพื่อสร้างฐานข้อมูลให้เรียบร้อย`
        });
      }
    }

    // 2. เพิ่มโครงการใหม่ (POST)
    if (req.method === 'POST') {
      const { id, title, description, original_url, image_url, status } = req.body;

      if (!id || !title || !original_url) {
        return res.status(400).json({ status: 'error', message: 'กรุณากรอกข้อมูลฟิลด์ที่จำเป็น (id, title, original_url) ให้ครบถ้วน' });
      }

      try {
        await db.query(
          'INSERT INTO projects (id, title, description, original_url, image_url, status) VALUES (?, ?, ?, ?, ?, ?)',
          [id, title, description || '', original_url, image_url || '', status || 'published']
        );
        return res.status(201).json({ status: 'success', message: 'บันทึกโปรเจกต์ลงฐานข้อมูลเรียบร้อยแล้ว' });
      } catch (dbError) {
        console.error('MySQL insert error:', dbError);
        return res.status(500).json({
          status: 'error',
          message: `เกิดข้อผิดพลาดในการบันทึกข้อมูล: ${dbError.message}`
        });
      }
    }

    // 3. แก้ไขข้อมูลโครงการ (PUT)
    if (req.method === 'PUT') {
      const { id, title, description, original_url, image_url, status } = req.body;

      if (!id || !title || !original_url) {
        return res.status(400).json({ status: 'error', message: 'กรุณาระบุข้อมูลฟิลด์ที่จำเป็นสำหรับการอัปเดต' });
      }

      try {
        const [result] = await db.query(
          'UPDATE projects SET title = ?, description = ?, original_url = ?, image_url = ?, status = ? WHERE id = ?',
          [title, description || '', original_url, image_url || '', status || 'published', id]
        );

        if (result.affectedRows === 0) {
          return res.status(404).json({ status: 'error', message: 'ไม่พบโปรเจกต์ที่ต้องการแก้ไขในฐานข้อมูล' });
        }

        return res.status(200).json({ status: 'success', message: 'อัปเดตข้อมูลโปรเจกต์ในฐานข้อมูลเรียบร้อยแล้ว' });
      } catch (dbError) {
        console.error('MySQL update error:', dbError);
        return res.status(500).json({
          status: 'error',
          message: `เกิดข้อผิดพลาดในการแก้ไขข้อมูล: ${dbError.message}`
        });
      }
    }

    // 4. ลบโครงการออกจากระบบ (DELETE)
    if (req.method === 'DELETE') {
      const { id } = req.body;

      if (!id) {
        return res.status(400).json({ status: 'error', message: 'กรุณาระบุ id ของโปรเจกต์ที่ต้องการลบ' });
      }

      try {
        const [result] = await db.query('DELETE FROM projects WHERE id = ?', [id]);

        if (result.affectedRows === 0) {
          return res.status(404).json({ status: 'error', message: 'ไม่พบโปรเจกต์ที่ต้องการลบในฐานข้อมูล' });
        }

        return res.status(200).json({ status: 'success', message: 'ลบโปรเจกต์ออกจากฐานข้อมูลเรียบร้อยแล้ว' });
      } catch (dbError) {
        console.error('MySQL delete error:', dbError);
        return res.status(500).json({
          status: 'error',
          message: `เกิดข้อผิดพลาดในการลบข้อมูล: ${dbError.message}`
        });
      }
    }

    // วิธีอื่นที่ยังไม่รองรับ
    return res.status(405).json({ status: 'error', message: 'Method not allowed' });

  } catch (err) {
    console.error('Server error:', err);
    return res.status(500).json({ status: 'error', message: `เกิดข้อผิดพลาดภายในระบบเซิร์ฟเวอร์: ${err.message}` });
  }
}
