-- Supabase Cloud Database Table Configuration SQL Script
-- คัดลอกสคริปต์นี้ไปวางและกด Run ในหน้าเมนู "SQL Editor" บน Supabase Dashboard ของคุณ

-- 1. สร้างตาราง projects สำหรับเก็บข้อมูลผลงานสะสม
CREATE TABLE IF NOT EXISTS projects (
    id TEXT PRIMARY KEY,
    title TEXT NOT NULL,
    description TEXT,
    original_url TEXT NOT NULL,
    image_url TEXT,
    status TEXT DEFAULT 'published',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc'::text, NOW()) NOT NULL
);

-- 2. เปิดใช้งานระบบ Row Level Security (RLS) และสร้างนโยบายอนุญาตให้แก้ไขข้อมูล
-- เพื่อให้ API ของ Node.js / Vercel สามารถอ่านและเขียนฐานข้อมูลได้โดยไม่มีปัญหาเรื่องสิทธิ์ RLS
ALTER TABLE projects ENABLE ROW LEVEL SECURITY;

-- นโยบายที่ 1: อนุญาตให้ทุกคนสามารถดึงข้อมูลผลงานไปแสดงผลได้ (SELECT)
CREATE POLICY "Allow public read access" ON projects
    FOR SELECT USING (true);

-- นโยบายที่ 2: อนุญาตให้ระบบหลังบ้านสามารถเพิ่มผลงานใหม่ได้ (INSERT)
CREATE POLICY "Allow public write access" ON projects
    FOR INSERT WITH CHECK (true);

-- นโยบายที่ 3: อนุญาตให้ระบบหลังบ้านสามารถแก้ไขผลงานได้ (UPDATE)
CREATE POLICY "Allow public update access" ON projects
    FOR UPDATE USING (true);

-- นโยบายที่ 4: อนุญาตให้ระบบหลังบ้านสามารถลบผลงานได้ (DELETE)
CREATE POLICY "Allow public delete access" ON projects
    FOR DELETE USING (true);
