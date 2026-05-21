<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio Management</title>
    
    <!-- Meta Tags สำหรับ SEO & Testing -->
    <meta name="description" content="ระบบจัดการข้อมูลผลงานและโปรเจกต์ ดึงข้อมูลเว็บไซต์เป้าหมายอัตโนมัติ">
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        outfit: ['Outfit', 'Inter', 'sans-serif'],
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        darkBg: '#090D16',
                        darkCard: '#111726',
                        darkBorder: '#1F293D',
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #090D16;
        }
        .font-title {
            font-family: 'Outfit', sans-serif;
        }
        .glass-panel {
            background: #111726;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }
        .input-dark {
            background-color: #0B0F19;
            border: 1px solid #1F293D;
            color: #F3F4F6;
        }
        .input-dark:focus {
            border-color: #6366F1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
            outline: none;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #111726;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #1F293D;
            border-radius: 3px;
        }
    </style>
</head>
<body class="text-gray-100 min-h-screen selection:bg-indigo-500 selection:text-white flex flex-col justify-between overflow-x-hidden">

    <!-- Glowing Background Accents -->
    <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-900/10 blur-[130px] pointer-events-none -z-10"></div>
    <div class="absolute bottom-[20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-purple-900/10 blur-[120px] pointer-events-none -z-10"></div>

    <!-- Header Navigation -->
    <header class="sticky top-0 z-40 bg-darkBg/80 backdrop-blur-md border-b border-darkBorder/60">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('portfolio.index') }}" class="text-gray-400 hover:text-white transition duration-200">
                    ← หน้าหลัก Portfolio
                </a>
                <span class="text-darkBorder">|</span>
                <span class="font-title text-xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                    แผงควบคุมระบบ (Admin Panel)
                </span>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-sm font-semibold text-white">Administrator</span>
                    <span class="text-xs text-gray-500">XAMPP Environment</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center font-bold text-white font-title">
                    AD
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-10 w-full flex-grow">
        
        <!-- Alerts Block -->
        @if(session('success'))
            <div class="mb-8 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center gap-3 animate-fade-in">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-8 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center gap-3 animate-fade-in">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Stats Section -->
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
            <div class="glass-panel p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">โปรเจกต์ทั้งหมด</span>
                    <span class="text-3xl font-bold font-title text-white">{{ $stats['total'] }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </div>
            </div>
            <div class="glass-panel p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">แสดงผลแล้ว</span>
                    <span class="text-3xl font-bold font-title text-emerald-400">{{ $stats['published'] }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="glass-panel p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">แบบร่าง</span>
                    <span class="text-3xl font-bold font-title text-amber-400">{{ $stats['draft'] }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
        </section>

        <!-- Two Columns Layout for Form and Preview -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
            
            <!-- Left Side: Fetch and Form Link -->
            <div class="lg:col-span-7 glass-panel p-6 sm:p-8 rounded-2xl">
                <h2 class="font-title text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 block"></span>
                    เพิ่มผลงานด้วยการดึงข้อมูลจาก URL
                </h2>

                <!-- 1. Search/Fetch Bar (ดึงข้อมูล) -->
                <div class="mb-8">
                    <label for="target_url" class="block text-sm font-semibold text-gray-300 mb-2">ป้อนที่อยู่เว็บไซต์ต้นทาง (Target URL)</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input 
                            type="url" 
                            id="target_url" 
                            placeholder="https://example.com/my-project" 
                            class="flex-grow px-4 py-3 rounded-xl input-dark text-sm"
                            required
                        >
                        <button 
                            type="button" 
                            id="btn-fetch"
                            class="px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] text-white font-semibold text-sm transition duration-200 flex items-center justify-center gap-2 whitespace-nowrap shadow-lg shadow-indigo-600/20"
                        >
                            <span id="fetch-text">ดึงข้อมูล OG ⚡</span>
                            <!-- Loading Spinner -->
                            <svg id="fetch-spinner" class="hidden animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        ระบบจะทำการดึงข้อมูล `og:image`, `og:title`, `og:description` จาก URL ด้านบนเพื่อความสะดวกรวดเร็ว
                    </p>
                </div>

                <!-- 2. Final Submission Form (บันทึกลงฐานข้อมูล) -->
                <form action="{{ route('admin.projects.store') }}" method="POST" id="save-project-form" class="hidden space-y-5 border-t border-darkBorder pt-6 animate-fade-in">
                    @csrf
                    
                    <!-- Hidden or Editable URL -->
                    <div>
                        <label for="form_original_url" class="block text-sm font-semibold text-gray-300 mb-2">URL ของโปรเจกต์</label>
                        <input 
                            type="url" 
                            name="original_url" 
                            id="form_original_url" 
                            class="w-full px-4 py-2.5 rounded-xl input-dark text-sm bg-gray-950/40 select-all" 
                            readonly 
                            required
                        >
                    </div>

                    <!-- Editable Title -->
                    <div>
                        <label for="form_title" class="block text-sm font-semibold text-gray-300 mb-2">หัวข้อเรื่อง (Title)</label>
                        <input 
                            type="text" 
                            name="title" 
                            id="form_title" 
                            placeholder="ป้อนชื่อโปรเจกต์"
                            class="w-full px-4 py-2.5 rounded-xl input-dark text-sm" 
                            required
                        >
                    </div>

                    <!-- Editable Description -->
                    <div>
                        <label for="form_description" class="block text-sm font-semibold text-gray-300 mb-2">คำอธิบายรายละเอียด (Description)</label>
                        <textarea 
                            name="description" 
                            id="form_description" 
                            rows="3" 
                            placeholder="คำอธิบายผลงานชิ้นนี้"
                            class="w-full px-4 py-2.5 rounded-xl input-dark text-sm"
                        ></textarea>
                    </div>

                    <!-- Editable Image URL -->
                    <div>
                        <label for="form_image_url" class="block text-sm font-semibold text-gray-300 mb-2">ลิงก์ที่อยู่รูปภาพ (Image URL)</label>
                        <input 
                            type="text" 
                            name="image_url" 
                            id="form_image_url" 
                            placeholder="https://example.com/image.jpg"
                            class="w-full px-4 py-2.5 rounded-xl input-dark text-sm"
                        >
                    </div>

                    <!-- Status Display Toggle -->
                    <div>
                        <label for="form_status" class="block text-sm font-semibold text-gray-300 mb-2">สถานะการแสดงผล</label>
                        <select 
                            name="status" 
                            id="form_status" 
                            class="px-4 py-2.5 rounded-xl input-dark text-sm bg-darkBg"
                            required
                        >
                            <option value="published">เผยแพร่สู่สาธารณะ (Published)</option>
                            <option value="draft">ร่างเก็บไว้ (Draft)</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 flex items-center gap-3">
                        <button 
                            type="submit" 
                            class="flex-grow px-5 py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-sm transition duration-200 flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/10"
                        >
                            ✨ บันทึกผลงานลงระบบ
                        </button>
                        <button 
                            type="button" 
                            id="btn-cancel"
                            class="px-5 py-3.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 font-semibold text-sm transition border border-white/5"
                        >
                            ยกเลิก
                        </button>
                    </div>
                </form>

                <!-- Status Empty Info for Form -->
                <div id="fetch-placeholder" class="text-center py-10 text-gray-500 border-t border-dashed border-darkBorder/80">
                    กรุณากรอก URL ในช่องด้านบนแล้วกดปุ่ม "ดึงข้อมูล" เพื่อแสดงฟอร์มบันทึกรายละเอียด
                </div>
            </div>

            <!-- Right Side: Live Interactive Preview Card -->
            <div class="lg:col-span-5 flex flex-col">
                <div class="glass-panel p-6 rounded-2xl flex-grow flex flex-col">
                    <h2 class="font-title text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 block"></span>
                        Live Preview Card (แสดงผลจำลอง)
                    </h2>

                    <!-- Main Preview Container -->
                    <div class="flex-grow flex items-center justify-center p-4 bg-darkBg/60 rounded-xl border border-darkBorder/40">
                        
                        <!-- Empty Preview State -->
                        <div id="preview-empty" class="text-center text-gray-500 max-w-xs py-10">
                            <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-sm">การ์ดโปรเจกต์ของคุณจะปรากฏที่นี่ในแบบเรียลไทม์เมื่อดึงข้อมูลเสร็จสิ้น</p>
                        </div>

                        <!-- Real Live Preview Card -->
                        <div id="preview-card" class="hidden w-full max-w-sm rounded-2xl overflow-hidden bg-white/5 border border-white/10 flex flex-col justify-between shadow-2xl relative transition-all duration-300">
                            <div>
                                <!-- Image Preview -->
                                <div class="relative aspect-video w-full bg-gray-950 overflow-hidden">
                                    <img 
                                        id="prev-img" 
                                        src="" 
                                        alt="Preview Image" 
                                        class="w-full h-full object-cover"
                                        onerror="this.style.display='none'; document.getElementById('prev-gradient').style.display='flex';"
                                    >
                                    <!-- Fallback Gradient block in preview -->
                                    <div id="prev-gradient" class="hidden w-full h-full bg-gradient-to-br from-violet-800 to-indigo-900 flex items-center justify-center p-6 text-center select-none">
                                        <div id="prev-gradient-title" class="font-title text-sm font-bold text-white/90 line-clamp-2 px-2">Project Preview</div>
                                    </div>
                                    <div class="absolute inset-0 bg-indigo-950/60 opacity-0 hover:opacity-100 transition duration-300 flex items-center justify-center z-10">
                                        <span class="text-white text-xs font-semibold px-3 py-1.5 bg-indigo-600 rounded-lg">เยี่ยมชมเว็บไซต์ ↗</span>
                                    </div>
                                </div>

                                <!-- Body Preview -->
                                <div class="p-5">
                                    <div id="prev-domain" class="text-xs text-indigo-400 font-semibold mb-1 uppercase tracking-wider truncate">
                                        DOMAIN.COM
                                    </div>
                                    <h3 id="prev-title" class="font-title text-lg font-bold text-white mb-2 line-clamp-1">
                                        ชื่อโครงการจำลอง
                                    </h3>
                                    <p id="prev-desc" class="text-gray-400 text-xs leading-relaxed line-clamp-3">
                                        รายละเอียดผลงานและคำอธิบายเบื้องต้นที่ถูกดึงมาจาก Metadata เว็บไซต์ปลายทาง
                                    </p>
                                </div>
                            </div>

                            <div class="px-5 pb-5 pt-1">
                                <div class="w-full py-2 px-4 rounded-xl bg-white/5 border border-white/5 text-center text-[10px] font-semibold text-gray-400 flex items-center justify-center gap-1.5">
                                    Link URL ต้นฉบับ
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </section>

        <!-- Existing Projects List (ตารางจัดการ) -->
        <section class="glass-panel rounded-2xl p-6 sm:p-8">
            <div class="border-b border-darkBorder pb-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="font-title text-xl font-bold text-white">รายการผลงานที่ลงทะเบียนไว้</h2>
                    <p class="text-sm text-gray-500 mt-0.5">รายการโครงการและเว็บไซต์ในพอร์ตโฟลิโอปัจจุบัน</p>
                </div>
                <div class="text-xs text-gray-400 px-3 py-1.5 rounded-lg bg-darkBg border border-darkBorder">
                    จำนวนโปรเจกต์รวม: <span class="text-indigo-400 font-bold">{{ $projects->count() }}</span>
                </div>
            </div>

            @if($projects->isEmpty())
                <div class="text-center py-16 text-gray-500">
                    <svg class="w-10 h-10 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    ไม่มีผลงานในระบบ จัดการดึง URL ลิงก์เพื่อจัดแสดงที่นี่ได้เลย
                </div>
            @else
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead>
                            <tr class="text-gray-400 border-b border-darkBorder/60 pb-3 text-xs uppercase tracking-wider">
                                <th class="py-3.5 px-4 font-semibold">โครงการ (Project Title)</th>
                                <th class="py-3.5 px-4 font-semibold">ลิงก์ปลายทาง (Source URL)</th>
                                <th class="py-3.5 px-4 font-semibold text-center">สถานะ</th>
                                <th class="py-3.5 px-4 font-semibold text-center">วันที่จัดเก็บ</th>
                                <th class="py-3.5 px-4 font-semibold text-right">ดำเนินการ (Actions)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-darkBorder/40">
                            @foreach($projects as $project)
                                <tr class="hover:bg-white/[0.01] transition-colors">
                                    <!-- Title and Preview image -->
                                    <td class="py-4 px-4 flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-950 flex-shrink-0">
                                            @if($project->image_url)
                                                <img src="{{ $project->image_url }}" alt="thumb" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-violet-600 to-indigo-800"></div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-200 max-w-[200px] truncate" title="{{ $project->title }}">
                                                {{ $project->title }}
                                            </span>
                                            <span class="text-xs text-gray-500 truncate max-w-[200px]" title="{{ $project->description }}">
                                                {{ $project->description }}
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- URL -->
                                    <td class="py-4 px-4 text-xs font-mono text-indigo-400/80">
                                        <a href="{{ $project->original_url }}" target="_blank" class="hover:underline hover:text-indigo-400 transition truncate max-w-[250px] inline-block">
                                            {{ $project->original_url }}
                                        </a>
                                    </td>
                                    
                                    <!-- Status -->
                                    <td class="py-4 px-4 text-center">
                                        @if($project->status == 'published')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                Public
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-500/15 text-gray-400 border border-white/5">
                                                Draft
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Date -->
                                    <td class="py-4 px-4 text-xs text-gray-500 text-center">
                                        {{ $project->created_at->format('d/m/Y H:i') }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 px-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a 
                                                href="{{ $project->original_url }}" 
                                                target="_blank"
                                                class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white border border-white/5 transition duration-150"
                                                title="เปิดดูเว็บจริง"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                            
                                            <!-- Delete Form -->
                                            <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบโปรเจกต์นี้?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="p-2 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 text-rose-400 hover:text-rose-300 transition duration-150"
                                                    title="ลบผลงาน"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

    </main>

    <!-- Footer -->
    <footer class="mt-20 border-t border-darkBorder/40 py-8 text-center text-xs text-gray-600 bg-darkBg/30">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                &copy; {{ date('Y') }} Portfolio Management Panel. แดชบอร์ดผู้ดูแลระบบ
            </div>
            <div>
                พัฒนาโดยใช้ Laravel 11/12 + Tailwind CSS & MySQL
            </div>
        </div>
    </footer>

    <!-- Interactive script สำหรับดึงข้อมูล และทำ Live Preview -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // Elements
            const targetUrlInput = document.getElementById('target_url');
            const btnFetch = document.getElementById('btn-fetch');
            const fetchText = document.getElementById('fetch-text');
            const fetchSpinner = document.getElementById('fetch-spinner');
            const fetchPlaceholder = document.getElementById('fetch-placeholder');
            
            const saveProjectForm = document.getElementById('save-project-form');
            const formOriginalUrl = document.getElementById('form_original_url');
            const formTitle = document.getElementById('form_title');
            const formDescription = document.getElementById('form_description');
            const formImageUrl = document.getElementById('form_image_url');
            const btnCancel = document.getElementById('btn-cancel');

            // Preview elements
            const previewEmpty = document.getElementById('preview-empty');
            const previewCard = document.getElementById('preview-card');
            const prevImg = document.getElementById('prev-img');
            const prevGradient = document.getElementById('prev-gradient');
            const prevGradientTitle = document.getElementById('prev-gradient-title');
            const prevDomain = document.getElementById('prev-domain');
            const prevTitle = document.getElementById('prev-title');
            const prevDesc = document.getElementById('prev-desc');

            // 1. URL Fetching handler
            btnFetch.addEventListener('click', async function () {
                const url = targetUrlInput.value.trim();

                if (!url) {
                    alert('กรุณากรอก URL ที่ต้องการดึงข้อมูลก่อนครับ');
                    return;
                }

                // Simple regex validation
                try {
                    new URL(url);
                } catch (_) {
                    alert('โปรดระบุ URL ที่ถูกต้อง เช่น https://github.com');
                    return;
                }

                // Start Loading state
                btnFetch.disabled = true;
                fetchText.style.display = 'none';
                fetchSpinner.classList.remove('hidden');

                try {
                    const response = await fetch("{{ route('admin.fetch-metadata') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ url: url })
                    });

                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        const data = result.data;

                        // 2. Populate form fields
                        formOriginalUrl.value = data.original_url;
                        formTitle.value = data.title;
                        formDescription.value = data.description;
                        formImageUrl.value = data.image_url || '';

                        // Show form & Hide Placeholder
                        saveProjectForm.classList.remove('hidden');
                        fetchPlaceholder.style.display = 'none';

                        // 3. Update & Show Preview Card
                        updatePreviewCard(data.original_url, data.title, data.description, data.image_url);

                    } else {
                        alert(result.message || 'เกิดข้อผิดพลาดในการดึงข้อมูลจากเว็บไซต์ต้นทาง');
                    }

                } catch (error) {
                    console.error('Fetch Error:', error);
                    alert('เกิดข้อผิดพลาดทางเทคนิคในการส่งข้อมูล: ' + error.message);
                } finally {
                    // Reset Loading state
                    btnFetch.disabled = false;
                    fetchText.style.display = 'inline';
                    fetchSpinner.classList.add('hidden');
                }
            });

            // Cancel button handler
            btnCancel.addEventListener('click', function () {
                saveProjectForm.reset();
                saveProjectForm.classList.add('hidden');
                fetchPlaceholder.style.display = 'block';

                previewCard.classList.add('hidden');
                previewEmpty.classList.remove('hidden');
            });

            // 4. Live Sync Form Inputs with Preview Card
            formTitle.addEventListener('input', function () {
                const titleVal = this.value.trim() || 'ชื่อโครงการจำลอง';
                prevTitle.textContent = titleVal;
                prevGradientTitle.textContent = titleVal;
            });

            formDescription.addEventListener('input', function () {
                prevDesc.textContent = this.value.trim() || 'คำอธิบายผลงานเบื้องต้น';
            });

            formImageUrl.addEventListener('input', function () {
                const imgVal = this.value.trim();
                if (imgVal) {
                    prevImg.src = imgVal;
                    prevImg.style.display = 'block';
                    prevGradient.style.display = 'none';
                } else {
                    prevImg.style.display = 'none';
                    prevGradient.style.display = 'flex';
                }
            });

            // Helper to update preview card
            function updatePreviewCard(url, title, description, imageUrl) {
                // Set Domain Name
                try {
                    const host = new URL(url).hostname;
                    prevDomain.textContent = host;
                } catch (_) {
                    prevDomain.textContent = 'LINK.COM';
                }

                prevTitle.textContent = title || 'ชื่อโครงการจำลอง';
                prevGradientTitle.textContent = title || 'ชื่อโครงการจำลอง';
                prevDesc.textContent = description || 'ไม่มีคำอธิบายสำหรับลิงก์นี้';

                if (imageUrl) {
                    prevImg.src = imageUrl;
                    prevImg.style.display = 'block';
                    prevGradient.style.display = 'none';
                } else {
                    prevImg.style.display = 'none';
                    prevGradient.style.display = 'flex';
                }

                // Show Card & Hide Empty state
                previewEmpty.classList.add('hidden');
                previewCard.classList.remove('hidden');
            }

        });
    </script>
</body>
</html>
