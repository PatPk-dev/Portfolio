import { createClient } from '@supabase/supabase-js';
import fs from 'fs';
import path from 'path';

// โหลดตัวแปรสภาพแวดล้อมจากไฟล์ .env แบบแมนนวลกรณีรันแบบธรรมดา (Local Development)
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

// ฟังก์ชันสร้าง Supabase Client
function getSupabaseClient() {
  const url = process.env.SUPABASE_URL;
  const key = process.env.SUPABASE_ANON_KEY;

  if (!url || !key || url.startsWith('YOUR_SUPABASE_') || key.startsWith('YOUR_SUPABASE_')) {
    return null;
  }
  return createClient(url, key);
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

  const supabase = getSupabaseClient();

  if (!supabase) {
    return res.status(500).json({
      status: 'error',
      type: 'supabase_config_error',
      message: 'กรุณากรอกและตั้งค่าตัวแปรสภาพแวดล้อม SUPABASE_URL และ SUPABASE_ANON_KEY ในไฟล์ .env หรือระบบคลาวด์ Vercel Dashboard ให้เรียบร้อยเพื่อเริ่มต้นใช้งานระบบผลงานสะสม'
    });
  }

  try {
    // 1. ดึงข้อมูลโครงการทั้งหมด (GET)
    if (req.method === 'GET') {
      const { data, error } = await supabase
        .from('projects')
        .select('*')
        .order('created_at', { ascending: false });

      if (error) throw error;
      return res.status(200).json({ status: 'success', data: data || [] });
    }

    // 2. เพิ่มโครงการใหม่ (POST)
    if (req.method === 'POST') {
      const { id, title, description, original_url, image_url, status } = req.body;

      if (!id || !title || !original_url) {
        return res.status(400).json({ status: 'error', message: 'กรุณากรอกข้อมูลฟิลด์ที่จำเป็น (id, title, original_url) ให้ครบถ้วน' });
      }

      const { data, error } = await supabase
        .from('projects')
        .insert([
          { 
            id, 
            title, 
            description: description || '', 
            original_url, 
            image_url: image_url || '', 
            status: status || 'published' 
          }
        ])
        .select();

      if (error) throw error;
      return res.status(201).json({ status: 'success', message: 'บันทึกโปรเจกต์ลง Supabase เรียบร้อยแล้ว', data });
    }

    // 3. แก้ไขข้อมูลโครงการ (PUT)
    if (req.method === 'PUT') {
      const { id, title, description, original_url, image_url, status } = req.body;

      if (!id || !title || !original_url) {
        return res.status(400).json({ status: 'error', message: 'กรุณาระบุข้อมูลฟิลด์ที่จำเป็นสำหรับการอัปเดต' });
      }

      const { data, error } = await supabase
        .from('projects')
        .update({ 
          title, 
          description: description || '', 
          original_url, 
          image_url: image_url || '', 
          status: status || 'published' 
        })
        .eq('id', id)
        .select();

      if (error) throw error;

      if (!data || data.length === 0) {
        return res.status(404).json({ status: 'error', message: 'ไม่พบโปรเจกต์ที่ต้องการแก้ไขใน Supabase' });
      }

      return res.status(200).json({ status: 'success', message: 'อัปเดตข้อมูลโปรเจกต์ใน Supabase เรียบร้อยแล้ว', data });
    }

    // 4. ลบโครงการออกจากระบบ (DELETE)
    if (req.method === 'DELETE') {
      const { id } = req.body;

      if (!id) {
        return res.status(400).json({ status: 'error', message: 'กรุณาระบุ id ของโปรเจกต์ที่ต้องการลบ' });
      }

      const { data, error } = await supabase
        .from('projects')
        .delete()
        .eq('id', id)
        .select();

      if (error) throw error;

      if (!data || data.length === 0) {
        return res.status(404).json({ status: 'error', message: 'ไม่พบโปรเจกต์ที่ต้องการลบใน Supabase' });
      }

      return res.status(200).json({ status: 'success', message: 'ลบโปรเจกต์ออกจาก Supabase เรียบร้อยแล้ว' });
    }

    // วิธีอื่นที่ยังไม่รองรับ
    return res.status(405).json({ status: 'error', message: 'Method not allowed' });

  } catch (err) {
    console.error('Supabase API error:', err);
    return res.status(500).json({ status: 'error', message: `เกิดข้อผิดพลาดในการเชื่อมต่อ Supabase: ${err.message}` });
  }
}
