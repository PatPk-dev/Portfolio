# PEERAPAT.K - Premium Glassmorphic Portfolio & Control Center

พอร์ตโฟลิโอสะสมผลงานระดับพรีเมียมของ **Peerapat Kunsubstid** ออกแบบด้วยดีไซน์กระจกสะท้อนแสง (Glassmorphism) สวยงามล้ำสมัย และระบบแผงควบคุมหลังบ้านที่ขับเคลื่อนด้วยฐานข้อมูลคลาวด์ **Supabase (PostgreSQL Cloud)** พร้อมเครื่องมือวิเคราะห์เมทาแท็กและเพิ่มลิงก์ชิ้นงานแบบกลุ่มในเวลาเดียวกัน (Bulk Add URL System) เชื่อมต่อแบบไร้รอยต่อกับ Vercel Serverless Functions

---

## ✨ คุณลักษณะเด่น (Features)
- **Premium Glassmorphic Design**: รูปลักษณ์สวยงามระดับพรีเมียมด้วย HSL Tailwind/CSS, การไล่สีที่หรูหรา และเอฟเฟกต์โฮเวอร์ที่ตอบสนองอย่างรวดเร็ว
- **Database-Driven 100% (Supabase PostgreSQL)**: การสื่อสารแบบเรียลไทม์กับคลาวด์ดาต้าเบส ปราศจาก LocalStorage แคช ทำให้ผลงานของคุณซิงก์กันในทุกบราวเซอร์ทันที
- **Admin Control Panel**: ระบบหลังบ้านสำหรับจัดการผลงานสะสมอย่างง่ายดาย:
  - เพิ่ม/แก้ไขชิ้นงานด้วยตนเอง พร้อม Live Card Preview แบบเรียลไทม์
  - ระบบเปลี่ยนสถานะชิ้นงานอย่างรวดเร็ว (Published เผยแพร่ / Draft แบบร่าง)
  - ระบบลบผลงานสะสม
- **Bulk URL Scraper Module**: Paste ลิงก์โปรเจกต์มาหลาย ๆ บรรทัดพร้อมกัน ระบบจะทำการยิง AJAX Concurrency เพื่อดึงข้อมูล Open Graph (ชื่อเว็บ, คำอธิบาย, รูปภาพปก) จากเว็บนั้น ๆ และบันทึกลง Supabase ให้เองทันที!
- **Serverless Cloud Ready**: รองรับการนำทางผ่าน `vercel.json` ปลอดภัยสูงสุดด้วยตัวแปรสภาพแวดล้อม `.env`

---

## 🛠️ วิธีการติดตั้งและเริ่มต้นใช้งาน (Getting Started)

### 1. การจัดเตรียมฐานข้อมูล Supabase
1. เข้าไปที่ [Supabase.com](https://supabase.com) (สมัครใช้งานฟรี)
2. สร้างโปรเจกต์ใหม่และตั้งชื่อตามใจชอบ
3. ไปที่เมนู **SQL Editor** บนบอร์ดควบคุม แล้วเลือก **New Query**
4. เปิดไฟล์ `supabase.sql` ในโฟลเดอร์โครงการนี้ คัดลอกสคริปต์ทั้งหมดไปวาง และกดปุ่ม **Run** เพื่อสร้างตารางข้อมูลและกำหนดนโยบาย Row Level Security (RLS)

### 2. กำหนดค่าตัวแปรสภาพแวดล้อม (Local Environment Setup)
1. ไปที่เมนู **Project Settings -> API** ใน Supabase คัดลอกค่าความปลอดภัยดังนี้:
   - **Project URL**
   - **Anon Public API Key**
2. เปิดไฟล์ `.env` ที่อยู่ใน root directory ของโปรเจกต์ และแทนที่ค่าจำลองด้วยข้อมูลจริงของคุณ:
   ```env
   SUPABASE_URL=คัดลอกค่า URL มาวางที่นี่
   SUPABASE_ANON_KEY=คัดลอกค่า Anon Key มาวางที่นี่
   ```

### 3. รันโปรเจกต์ในเครื่องของคุณ (Local Development)
ตรวจสอบให้แน่ใจว่าคุณได้ติดตั้ง Node.js เรียบร้อยแล้ว:
```bash
# 1. ติดตั้ง Dependencies ของระบบ
npm install

# 2. รันระบบเซิร์ฟเวอร์ด้วย Vercel CLI (ในโหมด Dev เพื่อจำลอง Serverless)
npm start
```
ระบบจะเปิดเบราว์เซอร์และแสดงผลที่ลิงก์ `http://localhost:3000` โดยอัตโนมัติ

---

## 🚀 การ Deploy ขึ้น Vercel สำหรับใช้งานออนไลน์ (Production)
โครงการนี้ได้รับการตั้งค่าพร้อมอัปโหลดขึ้น Vercel ได้ทันทีผ่าน GitHub Integration:
1. ทำการ Push โค้ดทั้งหมดขึ้น GitHub Repository ของคุณ (ระบบได้ถูก push ขึ้นหลักที่ `https://github.com/PatPk-dev/Portfolio.git` เรียบร้อยแล้ว)
2. เชื่อมต่อโปรเจกต์กับ **Vercel Dashboard**
3. **สำคัญมาก**: ไปที่การตั้งค่าของโปรเจกต์ใน Vercel -> เข้าไปที่แท็บ **Settings -> Environment Variables** และเพิ่มตัวแปรสภาพแวดล้อมทั้งสองตัวลงไป:
   - `SUPABASE_URL`
   - `SUPABASE_ANON_KEY`
4. กดบันทึกและรัน Deploy ตัวเว็บจะออนไลน์แบบ 100% ทันทีโดยไม่ต้องเปิดคอมพิวเตอร์ของคุณทิ้งไว้!

---

## 📄 ใบอนุญาต (License)
โครงการนี้สร้างสรรค์ขึ้นเป็นทรัพย์สินส่วนบุคคลของ **Peerapat Kunsubstid** ลิขสิทธิ์ถูกต้องทั้งหมด
