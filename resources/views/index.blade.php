<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Talabalar va Foydalanuvchilar</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="https://public-frontend-cos.metadl.com/mgx/img/favicon.png" type="image/png">
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
                <button class="settings-btn" id="downloadExcel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M12 1v6m0 6v6" />
                        <path d="M1 12h6m6 0h6" />
                    </svg>
                    Yuklash
                </button>
                <script>
                    document.getElementById('downloadExcel').addEventListener('click', function() {
                        const table = document.getElementById('studentsTable');
                        const rows = Array.from(table.querySelectorAll('tr'));

                        // Jadvaldagi har bir katakni CSV formatga o‘tkazamiz
                        const csvContent = rows.map(row => {
                            const cols = Array.from(row.querySelectorAll('th, td'));
                            return cols.map(col => `"${col.innerText.replace(/"/g, '""')}"`).join(',');
                        }).join('\n');

                        // CSV ni Excel sifatida yuklash
                        const blob = new Blob(["\uFEFF" + csvContent], {
                            type: 'text/csv;charset=utf-8;'
                        });
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = 'students.xlsx'; // Excel ochadi, lekin aslida CSV
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    });
                </script>

            </div>
        </div>
    </header>

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
                    <button class="tab-btn active" data-tab="students">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                            <path d="M6 12v5c3 3 9 3 12 0v-5" />
                        </svg>
                        Talabalar
                    </button>
                    <button class="tab-btn" data-tab="users">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        Adminlar
                    </button>
                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: inline;"
                        onsubmit="return confirm('Haqiqatan ham tizimdan chiqmoqchimisiz?')">
                        @csrf
                        <button type="submit" class="tab-btn"
                            style="color: red; background: none; border: none; cursor: pointer;">
                            Logout
                        </button>
                    </form>


                </div>

                <!-- Students Tab -->
                <div class="tab-content active" id="students-tab">
                    <div class="table-card">
                        <div class="table-header">
                            <h2>Talabalar</h2>
                            <button class="add-btn" id="addStudentBtn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Yangi talaba
                            </button>
                        </div>
                        <div class="search-container">
                            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <path d="M21 21l-4.35-4.35" />
                            </svg>
                            <input type="text" id="studentSearch" placeholder="Qidirish..." class="search-input">
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
                        <div class="table-footer">
                            <span id="studentsCount">Jami: 0 ta talaba</span>
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
                            <input type="text" id="userSearch" placeholder="Qidirish..." class="search-input">
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
                        <div class="table-footer">
                            <span id="usersCount">Jami: 0 ta foydalanuvchi</span>
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
                    <input name="talaba_id" type="text" id="talaba_id">
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
                <div class="form-group">
                    <label for="studentHudud">Hudud</label>
                    <input name="hudud" type="text" id="studentHudud">
                </div>
                <!-- Doimiy yashash xaritasi -->
                <div id="mapPermanent" style="width: 100%; height: 400px; margin-top: 10px; border-radius:10px;">
                </div>
                <small>Xaritadan tanlasangiz quyidagi maydonlar avtomatik to‘ldiriladi</small>

                <div class="edu-center-row">
                    <div class="edu-center-field-group edu-center-half">
                        <label for="doimiy_yashash_viloyati">Viloyat</label>
                        <select id="doimiy_yashash_viloyati" name="doimiy_yashash_viloyati" class="edu-center-input">
                            <option value="" disabled selected>Viloyatni tanlang...</option>
                        </select>
                    </div>
                    <div class="edu-center-field-group edu-center-half">
                        <label for="doimiy_yashash_tumani">Tuman</label>
                        <select id="doimiy_yashash_tumani" name="doimiy_yashash_tumani" class="edu-center-input">
                            <option value="" disabled selected>Tumanni tanlang...</option>
                        </select>
                    </div>
                </div>

                <div class="edu-center-field-group">
                    <label for="doimiy_yashash_manzili">Manzil</label>
                    <input type="text" name="doimiy_yashash_manzili" id="doimiy_yashash_manzili"
                        placeholder="To'liq manzil">
                </div>

                <div class="edu-center-field-group">
                    <label for="doimiy_yashash_manzili_urli">URL</label>
                    <input type="url" name="doimiy_yashash_manzili_urli" id="doimiy_yashash_manzili_urli"
                        placeholder="Manzil URL avtomatik yoziladi">
                </div>

                <hr style="margin:20px 0; border:1px solid #ddd;">
                <h3>📍 Vaqtincha yashash joyi</h3>

                <!-- Vaqtincha yashash xaritasi -->
                <div id="mapTemporary" style="width: 100%; height: 400px; margin-top: 10px; border-radius:10px;">
                </div>
                <small>Xaritadan tanlasangiz quyidagi maydonlar avtomatik to‘ldiriladi</small>

                <div class="edu-center-row">
                    <div class="edu-center-field-group edu-center-half">
                        <label for="vaqtincha_yashash_viloyati">Viloyat</label>
                        <select id="vaqtincha_yashash_viloyati" name="vaqtincha_yashash_viloyati"
                            class="edu-center-input">
                            <option value="" disabled selected>Viloyatni tanlang...</option>
                        </select>
                    </div>
                    <div class="edu-center-field-group edu-center-half">
                        <label for="vaqtincha_yashash_tumani">Tuman</label>
                        <select id="vaqtincha_yashash_tumani" name="vaqtincha_yashash_tumani"
                            class="edu-center-input">
                            <option value="" disabled selected>Tumanni tanlang...</option>
                        </select>
                    </div>
                </div>

                <div class="edu-center-field-group">
                    <label for="vaqtincha_yashash_manzili">Manzil</label>
                    <input type="text" name="vaqtincha_yashash_manzili" id="vaqtincha_yashash_manzili"
                        placeholder="To'liq manzil">
                </div>

                <div class="edu-center-field-group">
                    <label for="vaqtincha_yashash_manzili_urli">URL</label>
                    <input type="url" name="vaqtincha_yashash_manzili_urli" id="vaqtincha_yashash_manzili_urli"
                        placeholder="Manzil URL avtomatik yoziladi">
                </div>

                <div class="form-group">
                    <label for="studentUyEgasi">Uy egasi</label>
                    <input name="uy_egasi" type="text" id="studentUyEgasi">
                </div>
                <div class="form-group">
                    <label for="studentUyEgasiTelefoni">Uy egasi telefoni</label>
                    <input name="uy_egasi_telefoni" type="text" id="studentUyEgasiTelefoni">
                </div>
                <div class="form-group">
                    <label for="yotoqxona_nomeri">Yotoqxona raqami</label>
                    <input name="yotoqxona_nomeri" type="text" id="yotoqxona_nomeri">
                </div>
                <div class="form-group">
                    <label for="studentNarxi">Narxi</label>
                    <input name="narx" type="text" id="studentNarxi">
                </div>
                <div class="form-group">
                    <label for="studentOtaOna">Ota - Oan</label>
                    <input name="ota_ona" type="text" id="studentOtaOna">
                </div>
                <div class="form-group">
                    <label for="studentOtaOnaTelefoni">Ota - Oan telefoni</label>
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
                    </select>
                </div>
                <div class="form-group">
                    <label for="userPassword">Parol *</label>
                    <input name="password" type="password" id="userPassword">
                </div>
                <div class="form-group">
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
    </script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>

<script>
    const districtsByRegion = {
        Toshkent: ["Bekobod", "Bo‘ka", "Chinoz", "Oqqo‘rg‘on", "Ohangaron", "Piskent", "Quyichirchiq",
            "Yuqorichirchiq", "Zangiota", "Toshkent tumani", "Parkent"
        ],
        Sirdaryo: ["Guliston", "Boyovut", "Sardoba", "Mirzaobod", "Hovos", "Oqoltin", "Sayxunobod",
            "Sirdaryo tumani"
        ],
        Jizzax: ["Jizzax shahri", "Arnasoy", "Baxmal", "Do‘stlik", "Forish", "G‘allaorol", "Sharof Rashidov",
            "Paxtakor", "Zomin", "Yangiobod", "Mirzacho‘l", "Gagarin"
        ],
        Samarqand: ["Samarqand shahri", "Bulung‘ur", "Jomboy", "Ishtixon", "Kattaqo‘rg‘on", "Narpay", "Oqdaryo",
            "Pastdarg‘om", "Payariq", "Qo‘shrabot", "Samarqand tumani", "Tayloq", "Urgut", "Chelak", "Ziyodin",
            "Kattaqo‘rg‘on shahri"
        ],
        Buxoro: ["Buxoro shahri", "Buxoro tumani", "G‘ijduvon", "Jondor", "Kogon shahri", "Kogon tumani", "Olot",
            "Peshku", "Qorako‘l", "Qorovulbozor", "Shofirkon"
        ],
        Navoiy: ["Navoiy shahri", "Zarafshon shahri", "Karmana", "Xatirchi", "Qiziltepa", "Navbahor", "Tomdi",
            "Uchquduq"
        ],
        Qashqadaryo: ["Qarshi shahri", "Qarshi tumani", "Shahrisabz shahri", "Shahrisabz tumani", "Kitob",
            "Yakkabog‘", "Chiroqchi", "Nishon", "Muborak", "Qamashi", "Koson", "Kasbi", "G‘uzor", "Mirishkor"
        ],
        Surxandaryo: ["Termiz shahri", "Angor", "Boysun", "Denov", "Jarqo‘rg‘on", "Muzrabot", "Oltinsoy", "Qiziriq",
            "Qumqo‘rg‘on", "Sariosiyo", "Sherobod", "Sho‘rchi", "Termiz tumani", "Uzun"
        ],
        Xorazm: ["Urganch shahri", "Bog‘ot", "Gurlan", "Hazorasp", "Xiva", "Qo‘shko‘pir", "Shovot", "Tuproqqal’a",
            "Urganch tumani", "Xonqa", "Yangibozor"
        ],
        Andijon: ["Andijon shahri", "Andijon tumani", "Asaka", "Baliqchi", "Bo‘z", "Buloqboshi", "Izboskan",
            "Jalolquduq", "Xo‘jaobod", "Qo‘rg‘ontepa", "Marhamat", "Oltinko‘l", "Paxtaobod", "Shahrixon",
            "Ulug‘nor", "Xonobod shahri"
        ],
        Namangan: ["Namangan shahri", "Chortoq", "Chust", "Kosonsoy", "Mingbuloq", "Norin", "Pop", "To‘raqo‘rg‘on",
            "Uychi", "Yangiqo‘rg‘on", "Namangan tumani"
        ],
        "Farg‘ona": ["Farg‘ona shahri", "Qo‘qon shahri", "Marg‘ilon shahri", "Oltiariq", "O‘zbekiston tumani",
            "Quva", "Rishton", "Toshloq", "Yozyovon", "Dang‘ara", "Beshariq", "Bog‘dod", "So‘x", "Uchko‘prik",
            "Furqat"
        ],
        "Qoraqalpog‘iston": ["Nukus shahri", "Amudaryo", "Beruniy", "Chimboy", "Ellikqal‘a", "Kegeyli", "Mo‘ynoq",
            "Nukus tumani", "Qanliko‘l", "Qo‘ng‘irot", "Qorao‘zak", "Shumanay", "Taxtako‘pir", "To‘rtko‘l",
            "Xo‘jayli", "Taxiatosh shahri"
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
