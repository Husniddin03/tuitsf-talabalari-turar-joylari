<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Talabalar va Foydalanuvchilar</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="https://public-frontend-cos.metadl.com/mgx/img/favicon.png" type="image/png">
    <style>
        .alert-custom {
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            text-align: center;
            margin-bottom: 15px;
            border: 1px solid transparent;
            width: 100%;
            display: block;
        }

        /* Xato (error) uchun */
        .alert-custom.error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        /* Muvaffaqiyat (success) uchun */
        .alert-custom.success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        @media (max-width: 768px) {
            .download-btn {
                flex-direction: column;
            }
            .download-btn button{
                width: 100%;
                margin-top: 8px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <svg class="logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3v18h18" />
                        <path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3" />
                    </svg>
                    <h1>Admin Panel</h1>
                </div>
                <div class="header-actions">
                    <span class="user-role" id="currentUserRole">{{ Auth::user()->role ?? 'user' }}</span>

                    <form id="logoutForm" action="{{ route('logout') }}" method="POST"
                        style="display: inline; margin: 0; padding: 0"
                        onsubmit="return confirm('Haqiqatan ham tizimdan chiqmoqchimisiz?')">
                        @csrf
                        <button style="color: red; margin: 0;" type="submit" class="settings-btn" id="">
                            <img width="24" height="24"
                                src="https://img.icons8.com/?size=100&id=LYzWbTKzKcac&format=png&color=000000"
                                alt="">
                            Chiqish
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    @if (session('error'))
        <div class="alert-custom error" role="alert">
            {{ session('error') }}
        </div>
    @elseif(session('success'))
        <div class="alert-custom success" role="alert">
            {{ session('success') }}
        </div>
    @endif


    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <h3>Jami Talabalar</h3>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                            <path d="M6 12v5c3 3 9 3 12 0v-5" />
                        </svg>
                    </div>
                    <div class="stat-number" id="totalStudents">0</div>
                    <p class="stat-description">Ro'yxatdan o'tgan talabalar</p>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3>Jami Foydalanuvchilar</h3>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <div class="stat-number" id="totalUsers">0</div>
                    <p class="stat-description">Tizimga kirgan foydalanuvchilar</p>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3>Fakultetlar</h3>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3v18h18" />
                            <path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3" />
                        </svg>
                    </div>
                    <div class="stat-number" id="totalFaculties">0</div>
                    <p class="stat-description">Turli fakultetlar</p>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3>Adminlar</h3>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M12 1v6m0 6v6" />
                            <path d="M1 12h6m6 0h6" />
                        </svg>
                    </div>
                    <div class="stat-number" id="totalAdmins">0</div>
                    <p class="stat-description">Admin foydalanuvchilar</p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <div class="tab-buttons">
                    <button class="tab-btn active" data-tab="students" id="studentsTab">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                            <path d="M6 12v5c3 3 9 3 12 0v-5" />
                        </svg>
                        Talabalar
                    </button>
                    <button class="tab-btn" data-tab="users" id="usersTab">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        Adminlar
                    </button>

                </div>

                <!-- Students Tab -->
                <div class="tab-content active" id="students-tab">
                    <div class="table-card">
                        <div class="table-header">
                            <h2>Talabalar</h2>
                            <div style="display: flex" class="download-btn">
                                <button style="margin-right: 8px" class="add-btn" id="addStudentBtn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    Yangi talaba
                                </button>
                                <button style="margin-right: 8px" class="settings-btn" id="downloadExcel">
                                    <img width="24" height="24"
                                        src="https://img.icons8.com/?size=100&id=1kX0jH69NbUF&format=png&color=000000"
                                        alt="">
                                    Saralanganlarni yuklash
                                </button>
                                <!-- CDN orqali SheetJS ni ulang -->
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

                                <script>
                                    document.getElementById('downloadExcel').addEventListener('click', function() {
                                        const table = document.getElementById('studentsTable');

                                        // Jadvalni Excel workbook ga o'tkazish
                                        const wb = XLSX.utils.table_to_book(table, {
                                            sheet: "Talabalar"
                                        });

                                        // Excel faylni yuklab olish
                                        XLSX.writeFile(wb, 'students.xlsx');
                                    });
                                </script>
                                <form style="margin: 0; padding: 0" action="{{ route('download') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="settings-btn" id="alldownloadExcel">
                                        <img width="24" height="24"
                                            src="https://img.icons8.com/?size=100&id=1kX0jH69NbUF&format=png&color=000000"
                                            alt="">
                                        Barcha talabalarni yuklash
                                    </button>
                                </form>
                            </div>

                        </div>
                        <div class="search-container">
                            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <path d="M21 21l-4.35-4.35" />
                            </svg>
                            <input type="text" id="studentSearch"
                                placeholder="Qidirish (ID, F.I.Sh, Fakultet, Guruh)..." class="search-input">
                            <div class="search-loading" id="studentSearchLoading" style="display: none;">
                                <div class="spinner"></div>
                            </div>
                        </div>
                        <div class="table-container">
                            <table class="data-table" id="studentsTable">
                                <thead>
                                    <tr>
                                        <th data-sort="id">Id <span class="sort-icon">↕</span></th>
                                        <th data-sort="talaba_id">Talaba Id <span class="sort-icon">↕</span></th>
                                        <th data-sort="fish">F.I.Sh <span class="sort-icon">↕</span></th>
                                        <th data-sort="fakultet">Fakultet <span class="sort-icon">↕</span></th>
                                        <th data-sort="guruh">Guruh <span class="sort-icon">↕</span></th>
                                        <th data-sort="telefon">Telefon <span class="sort-icon">↕</span></th>
                                        <th data-sort="tyutori">Tyutori <span class="sort-icon">↕</span></th>
                                        <th data-sort="hudud">Hudud <span class="sort-icon">↕</span></th>
                                        <th data-sort="doimiy_yashash_viloyati">Doimiy yashash viloyati <span
                                                class="sort-icon">↕</span></th>
                                        <th data-sort="doimiy_yashash_tumani">Doimiy yashash tumani <span
                                                class="sort-icon">↕</span></th>
                                        <th data-sort="doimiy_yashash_manzili">Doimiy yashash manzili <span
                                                class="sort-icon">↕</span></th>
                                        <th>Doimiy yashash manzili urli</th>
                                        <th data-sort="vaqtincha_yashash_viloyati">Vaqtincha yashash viloyati <span
                                                class="sort-icon">↕</span></th>
                                        <th data-sort="vaqtincha_yashash_tumani">Vaqtincha yashash tumani <span
                                                class="sort-icon">↕</span></th>
                                        <th data-sort="vaqtincha_yashash_manzili">Vaqtincha yashash manzili <span
                                                class="sort-icon">↕</span></th>
                                        <th>Vaqtincha yashash manzili urli</th>
                                        <th data-sort="uy_egasi">Uy egasi <span class="sort-icon">↕</span></th>
                                        <th data-sort="uy_egasi_telefoni">Uy egasi telefoni <span
                                                class="sort-icon">↕</span></th>
                                        <th data-sort="yotoqxona_nomeri">Yotoqxona nomeri <span
                                                class="sort-icon">↕</span></th>
                                        <th data-sort="narx">Narxi <span class="sort-icon">↕</span></th>
                                        <th data-sort="ota_ona">Ota ona <span class="sort-icon">↕</span></th>
                                        <th data-sort="ota_ona_telefoni">Ota ona telefoni <span
                                                class="sort-icon">↕</span></th>
                                        <th>Amallar</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTableBody">
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="pagination-container">
                            <div class="pagination-info">
                                <span id="studentsCount">Jami: 0 ta talaba</span>
                                <span id="studentsPageInfo">Sahifa 1 / 1</span>
                            </div>
                            <div class="pagination-controls">
                                <button class="pagination-btn" id="studentsPrevBtn" disabled>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="15,18 9,12 15,6"></polyline>
                                    </svg>
                                    Oldingi
                                </button>
                                <div class="pagination-pages" id="studentsPagination"></div>
                                <button class="pagination-btn" id="studentsNextBtn" disabled>
                                    Keyingi
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="9,18 15,12 9,6"></polyline>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Tab -->
                <div class="tab-content" id="users-tab">
                    <div class="table-card">
                        <div class="table-header">
                            <h2>Adminlar</h2>
                            <button class="add-btn" id="addUserBtn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Yangi admin
                            </button>
                        </div>
                        <div class="search-container">
                            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <path d="M21 21l-4.35-4.35" />
                            </svg>
                            <input type="text" id="userSearch" placeholder="Qidirish (Ism, Email, Rol)..."
                                class="search-input">
                            <div class="search-loading" id="userSearchLoading" style="display: none;">
                                <div class="spinner"></div>
                            </div>
                        </div>
                        <div class="table-container" style="width: 100% !important; overflow-x: auto !important">
                            <table class="data-table" id="usersTable"
                                style="width: 100%;  border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th data-sort="id">ID <span class="sort-icon">↕</span></th>
                                        <th data-sort="name">Ism <span class="sort-icon">↕</span></th>
                                        <th data-sort="email">Email <span class="sort-icon">↕</span></th>
                                        <th data-sort="chat_id">Chat ID <span class="sort-icon">↕</span></th>
                                        <th data-sort="role">Rol <span class="sort-icon">↕</span></th>
                                        <th data-sort="created_at">Yaratilgan <span class="sort-icon">↕</span></th>
                                        <th>Amallar</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="pagination-container">
                            <div class="pagination-info">
                                <span id="usersCount">Jami: 0 ta foydalanuvchi</span>
                                <span id="usersPageInfo">Sahifa 1 / 1</span>
                            </div>
                            <div class="pagination-controls">
                                <button class="pagination-btn" id="usersPrevBtn" disabled>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="15,18 9,12 15,6"></polyline>
                                    </svg>
                                    Oldingi
                                </button>
                                <div class="pagination-pages" id="usersPagination"></div>
                                <button class="pagination-btn" id="usersNextBtn" disabled>
                                    Keyingi
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="9,18 15,12 9,6"></polyline>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Student Modal -->
    <div class="modal" id="studentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="studentModalTitle">Yangi talaba qo'shish</h3>
                <button class="close-btn" id="closeStudentModal">×</button>
            </div>
            <form action="{{ route('web.store') }}" method="post" id="studentForm">
                @csrf
                <div class="form-group">
                    <label for="talaba_id">Talaba id *</label>
                    <input name="talaba_id" type="number" id="talaba_id">
                </div>
                <div class="form-group">
                    <label for="studentFish">F.I.Sh *</label>
                    <input name="fish" type="text" id="studentFish">
                </div>
                <div class="form-group">
                    <label for="studentFakultet">Fakultet *</label>
                    <input name="fakultet" type="text" id="studentFakultet">
                </div>
                <div class="form-group">
                    <label for="studentGuruh">Guruh *</label>
                    <input name="guruh" type="text" id="studentGuruh">
                </div>
                <div class="form-group">
                    <label for="studentTelefon">Telefon</label>
                    <input name="telefon" type="text" id="studentTelefon" placeholder="+998901234567">
                </div>
                <div class="form-group">
                    <label for="studentTyutori">Tyutori</label>
                    <input name="tyutori" type="text" id="studentTyutori">
                </div>
                <hr style="margin:20px 0; border:1px solid #ddd;">
                <h3>📍 Doimiy yashash joyi</h3>
                <div class="form-group">
                    <label for="studentHudud">Hudud</label>
                    <input name="hudud" type="text" id="studentHudud">
                </div>
                <!-- Doimiy yashash xaritasi -->
                <div id="mapPermanent" style="width: 100%; height: 400px; margin-top: 10px; border-radius:10px;">
                </div>
                <small>Xaritadan tanlasangiz quyidagi maydonlar avtomatik to'ldiriladi</small>

                <div class="form-group">
                    <label for="doimiy_yashash_viloyati">Viloyat</label>
                    <select id="doimiy_yashash_viloyati" name="doimiy_yashash_viloyati" class="edu-center-input">
                        <option value="" disabled selected>Viloyatni tanlang...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="doimiy_yashash_tumani">Tuman</label>
                    <select id="doimiy_yashash_tumani" name="doimiy_yashash_tumani" class="edu-center-input">
                        <option value="" disabled selected>Tumanni tanlang...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="doimiy_yashash_manzili">Manzil</label>
                    <input type="text" name="doimiy_yashash_manzili" id="doimiy_yashash_manzili"
                        placeholder="To'liq manzil">
                </div>

                <div class="form-group">
                    <label for="doimiy_yashash_manzili_urli">URL</label>
                    <input type="url" name="doimiy_yashash_manzili_urli" id="doimiy_yashash_manzili_urli"
                        placeholder="Manzil URL avtomatik yoziladi">
                </div>

                <hr style="margin:20px 0; border:1px solid #ddd;">
                <h3>📍 Vaqtincha yashash joyi</h3>

                <div class="form-group"
                    style="
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        margin: 12px 0;
                        padding: 10px 15px;
                        border: 1px solid #ddd;
                        border-radius: 8px;
                        background-color: #f9f9f9;
                        font-family: 'Poppins', sans-serif;
                        font-size: 18px;
                        color: #333;
                    ">
                    <label for="dormity" style="cursor: pointer; flex: 1;">Yotoqxona</label>
                    <input type="checkbox" name="dormity" id="dormity"
                        style="
                            width: 22px;
                            height: 22px;
                            cursor: pointer;
                            accent-color: #4a90e2;
                        ">
                </div>



                <!-- Vaqtincha yashash xaritasi -->
                <div id="ijara">
                    <div id="mapTemporary" style="width: 100%; height: 400px; margin-top: 10px; border-radius:10px;">
                    </div>
                    <small>Xaritadan tanlasangiz quyidagi maydonlar avtomatik to'ldiriladi</small>

                    <div class="form-group">
                        <label for="vaqtincha_yashash_viloyati">Viloyat</label>
                        <select id="vaqtincha_yashash_viloyati" name="vaqtincha_yashash_viloyati"
                            class="edu-center-input">
                            <option value="" disabled selected>Viloyatni tanlang...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="vaqtincha_yashash_tumani">Tuman</label>
                        <select id="vaqtincha_yashash_tumani" name="vaqtincha_yashash_tumani"
                            class="edu-center-input">
                            <option value="" disabled selected>Tumanni tanlang...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="vaqtincha_yashash_manzili">Manzil</label>
                        <input type="text" name="vaqtincha_yashash_manzili" id="vaqtincha_yashash_manzili"
                            placeholder="To'liq manzil">
                    </div>

                    <div class="form-group">
                        <label for="vaqtincha_yashash_manzili_urli">URL</label>
                        <input type="url" name="vaqtincha_yashash_manzili_urli"
                            id="vaqtincha_yashash_manzili_urli" placeholder="Manzil URL avtomatik yoziladi">
                    </div>

                    <div class="form-group">
                        <label for="studentUyEgasi">Uy egasi</label>
                        <input name="uy_egasi" type="text" id="studentUyEgasi">
                    </div>
                    <div class="form-group">
                        <label for="studentUyEgasiTelefoni">Uy egasi telefoni</label>
                        <input name="uy_egasi_telefoni" type="text" id="studentUyEgasiTelefoni">
                    </div>
                </div>

                <div id="yotoqxona" class="form-group">
                    <label for="yotoqxona_nomeri">Yotoqxona raqami</label>
                    <select id="yotoqxona_nomeri" name="yotoqxona_nomeri" class="edu-center-input">
                        <option value="" disabled selected>Yotoqxona nomerini tanlang...</option>
                        <option value="1-sonli">1-sonli</option>
                        <option value="2-sonli">2-sonli</option>
                        <option value="3-sonli">3-sonli</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="studentNarxi">Narxi</label>
                    <input name="narx" type="number" id="studentNarxi">
                </div>
                <div class="form-group">
                    <label for="studentOtaOna">Ota - Ona</label>
                    <input name="ota_ona" type="text" id="studentOtaOna">
                </div>
                <div class="form-group">
                    <label for="studentOtaOnaTelefoni">Ota - Ona telefoni</label>
                    <input name="ota_ona_telefoni" type="text" id="studentOtaOnaTelefoni">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary" id="saveStudentBtn">Saqlash</button>
                    <button type="button" class="btn-secondary" id="cancelStudentBtn">Bekor qilish</button>
                </div>
            </form>

        </div>
    </div>

    <!-- User Modal -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="userModalTitle">Yangi foydalanuvchi qo'shish</h3>
                <button class="close-btn" id="closeUserModal">×</button>
            </div>
            <form method="post" action="{{ route('admin.store') }}" id="userForm">
                <div class="form-group">
                    <label for="userName">Ism *</label>
                    <input name="name" type="text" id="userName">
                </div>
                <div class="form-group">
                    <label for="userEmail">Email *</label>
                    <input name="email" type="email" id="userEmail">
                </div>
                <div class="form-group">
                    <label for="userChatId">Chat ID *</label>
                    <input name="chat_id" type="number" id="userChatId">
                </div>
                <div class="form-group">
                    <label for="userRole">Rol *</label>
                    <select name="role" id="userRole">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="userPassword">Parol *</label>
                    <input name="password" type="password" id="userPassword">
                </div>
                <div hidden class="form-group">
                    <label for="userEmailVerified">Email tasdiqlangan vaqt</label>
                    <input name="email_verified_at" type="datetime-local" id="userEmailVerified">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary" id="saveUserBtn">Saqlash</button>
                    <button type="button" class="btn-secondary" id="cancelUserBtn">Bekor qilish</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Laravel ma'lumotlarini global scope ga qo'yamiz
        window.mockStudents1 = @json($students);
        window.mockUsers1 = @json($users);
        window.currentUserRole = '{{ Auth::user()->role ?? 'user' }}';
    </script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>

<script>
    const districtsByRegion = {
        Toshkent: ["Bekobod", "Bo'ka", "Chinoz", "Oqqo'rg'on", "Ohangaron", "Piskent", "Quyichirchiq",
            "Yuqorichirchiq", "Zangiota", "Toshkent tumani", "Parkent"
        ],
        Sirdaryo: ["Guliston", "Boyovut", "Sardoba", "Mirzaobod", "Hovos", "Oqoltin", "Sayxunobod",
            "Sirdaryo tumani"
        ],
        Jizzax: ["Jizzax shahri", "Arnasoy", "Baxmal", "Do'stlik", "Forish", "G'allaorol", "Sharof Rashidov",
            "Paxtakor", "Zomin", "Yangiobod", "Mirzacho'l", "Gagarin"
        ],
        Samarqand: ["Samarqand shahri", "Bulung'ur", "Jomboy", "Ishtixon", "Kattaqo'rg'on", "Narpay", "Oqdaryo",
            "Pastdarg'om", "Payariq", "Qo'shrabot", "Samarqand tumani", "Tayloq", "Urgut", "Chelak", "Ziyodin",
            "Kattaqo'rg'on shahri"
        ],
        Buxoro: ["Buxoro shahri", "Buxoro tumani", "G'ijduvon", "Jondor", "Kogon shahri", "Kogon tumani", "Olot",
            "Peshku", "Qorako'l", "Qorovulbozor", "Shofirkon"
        ],
        Navoiy: ["Navoiy shahri", "Zarafshon shahri", "Karmana", "Xatirchi", "Qiziltepa", "Navbahor", "Tomdi",
            "Uchquduq"
        ],
        Qashqadaryo: ["Qarshi shahri", "Qarshi tumani", "Shahrisabz shahri", "Shahrisabz tumani", "Kitob",
            "Yakkabog'", "Chiroqchi", "Nishon", "Muborak", "Qamashi", "Koson", "Kasbi", "G'uzor", "Mirishkor"
        ],
        Surxandaryo: ["Termiz shahri", "Angor", "Boysun", "Denov", "Jarqo'rg'on", "Muzrabot", "Oltinsoy", "Qiziriq",
            "Qumqo'rg'on", "Sariosiyo", "Sherobod", "Sho'rchi", "Termiz tumani", "Uzun"
        ],
        Xorazm: ["Urganch shahri", "Bog'ot", "Gurlan", "Hazorasp", "Xiva", "Qo'shko'pir", "Shovot", "Tuproqqal'a",
            "Urganch tumani", "Xonqa", "Yangibozor"
        ],
        Andijon: ["Andijon shahri", "Andijon tumani", "Asaka", "Baliqchi", "Bo'z", "Buloqboshi", "Izboskan",
            "Jalolquduq", "Xo'jaobod", "Qo'rg'ontepa", "Marhamat", "Oltinko'l", "Paxtaobod", "Shahrixon",
            "Ulug'nor", "Xonobod shahri"
        ],
        Namangan: ["Namangan shahri", "Chortoq", "Chust", "Kosonsoy", "Mingbuloq", "Norin", "Pop", "To'raqo'rg'on",
            "Uychi", "Yangiqo'rg'on", "Namangan tumani"
        ],
        "Farg'ona": ["Farg'ona shahri", "Qo'qon shahri", "Marg'ilon shahri", "Oltiariq", "O'zbekiston tumani",
            "Quva", "Rishton", "Toshloq", "Yozyovon", "Dang'ara", "Beshariq", "Bog'dod", "So'x", "Uchko'prik",
            "Furqat"
        ],
        "Qoraqalpog'iston": ["Nukus shahri", "Amudaryo", "Beruniy", "Chimboy", "Ellikqal'a", "Kegeyli", "Mo'ynoq",
            "Nukus tumani", "Qanliko'l", "Qo'ng'irot", "Qorao'zak", "Shumanay", "Taxtako'pir", "To'rtko'l",
            "Xo'jayli", "Taxiatosh shahri"
        ]
    };

    function fillRegions(regionSelectId, districtSelectId) {
        const regionSelect = document.getElementById(regionSelectId);
        const districtSelect = document.getElementById(districtSelectId);

        for (let region in districtsByRegion) {
            const option = document.createElement("option");
            option.value = region;
            option.textContent = region;
            regionSelect.appendChild(option);
        }

        regionSelect.addEventListener("change", () => {
            districtSelect.innerHTML = '<option value="" disabled selected>Tumanni tanlang...</option>';
            if (districtsByRegion[regionSelect.value]) {
                districtsByRegion[regionSelect.value].forEach(d => {
                    const opt = document.createElement("option");
                    opt.value = d;
                    opt.textContent = d;
                    districtSelect.appendChild(opt);
                });
            }
        });
    }

    fillRegions("doimiy_yashash_viloyati", "doimiy_yashash_tumani");
    fillRegions("vaqtincha_yashash_viloyati", "vaqtincha_yashash_tumani");

    let geocoder;

    function initMap() {
        geocoder = new google.maps.Geocoder();

        createMap("mapPermanent", {
            region: "doimiy_yashash_viloyati",
            district: "doimiy_yashash_tumani",
            address: "doimiy_yashash_manzili",
            url: "doimiy_yashash_manzili_urli"
        });

        createMap("mapTemporary", {
            region: "vaqtincha_yashash_viloyati",
            district: "vaqtincha_yashash_tumani",
            address: "vaqtincha_yashash_manzili",
            url: "vaqtincha_yashash_manzili_urli"
        });
    }

    function createMap(mapId, fields) {
        const defaultCenter = {
            lat: 41.3111,
            lng: 69.2797
        };

        const map = new google.maps.Map(document.getElementById(mapId), {
            center: defaultCenter,
            zoom: 12,
        });

        const marker = new google.maps.Marker({
            map: map,
            position: defaultCenter,
            draggable: true,
        });

        // Bosilganda
        map.addListener("click", (event) => {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            marker.setPosition({
                lat,
                lng
            });
            updateAddress(lat, lng, fields);
        });

        // Marker siljitilganda
        marker.addListener("dragend", (event) => {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            updateAddress(lat, lng, fields);
        });
    }

    function updateAddress(lat, lng, fields) {
        const addressField = document.getElementById(fields.address);
        const urlField = document.getElementById(fields.url);
        const regionField = document.getElementById(fields.region);
        const districtField = document.getElementById(fields.district);

        urlField.value = `https://www.google.com/maps?q=${lat},${lng}&hl=uz&z=14`;
        geocoder.geocode({
            location: {
                lat,
                lng
            }
        }, (results, status) => {
            if (status === "OK" && results[0]) {
                const address = results[0].formatted_address;
                addressField.value = address;

                // Viloyat va tuman ajratish
                const components = results[0].address_components;
                let regionName = "",
                    districtName = "";

                for (let c of components) {
                    if (c.types.includes("administrative_area_level_1")) {
                        regionName = c.long_name;
                    }
                    if (c.types.includes("administrative_area_level_2")) {
                        districtName = c.long_name;
                    }
                }

                // Mos keladigan viloyat va tumanlarni topish
                for (let region in districtsByRegion) {
                    if (address.includes(region)) {
                        regionField.value = region;
                        districtField.innerHTML = "";
                        districtsByRegion[region].forEach(d => {
                            const opt = document.createElement("option");
                            opt.value = d;
                            opt.textContent = d;
                            districtField.appendChild(opt);
                        });
                        const foundDistrict = districtsByRegion[region].find(d => address.includes(d));
                        if (foundDistrict) districtField.value = foundDistrict;
                    }
                }
            } else {
                addressField.value = "";
                urlField.value = "";
            }
        });
    }

    window.initMap = initMap;
</script>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAM-lcwS2aMgdJd5AMxE8N_1Lu7M3aHJUw&callback=initMap" async
    defer></script>


<script>
    const dormity = document.getElementById('dormity');
    const yotoqxona = document.getElementById('yotoqxona');
    const ijara = document.getElementById('ijara');

    yotoqxona.style.display = 'none';

    // Funksiya: berilgan element ichidagi input/selectlarni tozalaydi
    function clearInputs(container) {
        const inputs = container.querySelectorAll('input, select, textarea');
        inputs.forEach(el => {
            if (el.type === 'checkbox' || el.type === 'radio') {
                el.checked = false; // belgilarni olib tashlaydi
            } else {
                el.value = ''; // matn, raqam, select va boshqalarni tozalaydi
            }
        });
    }

    // Checkbox bosilganda ishlaydigan funksiya
    dormity.addEventListener('change', function() {
        if (this.checked) {
            // yotoqxona ko'rsatilsin, ijara yashirilsin
            ijara.style.display = 'none';
            yotoqxona.style.display = 'block';
            clearInputs(ijara); // yashirilayotgan bo'limdagi qiymatlarni tozalaydi
            console.log('check');
        } else {
            // ijara ko'rsatilsin, yotoqxona yashirilsin
            yotoqxona.style.display = 'none';
            ijara.style.display = 'block';
            clearInputs(yotoqxona); // yashirilayotgan bo'limdagi qiymatlarni tozalaydi
            console.log('nocheck');
        }
    });
</script>
