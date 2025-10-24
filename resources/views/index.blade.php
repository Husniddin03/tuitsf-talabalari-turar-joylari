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
                                        <th data-sort="id">ID <span class="sort-icon">↕</span></th>
                                        <th data-sort="fish">F.I.Sh <span class="sort-icon">↕</span></th>
                                        <th data-sort="fakultet">Fakultet <span class="sort-icon">↕</span></th>
                                        <th data-sort="guruh">Guruh <span class="sort-icon">↕</span></th>
                                        <th data-sort="telefon">Telefon <span class="sort-icon">↕</span></th>
                                        <th data-sort="tyutori">Tyutori <span class="sort-icon">↕</span></th>
                                        <th data-sort="hudud">Hudud <span class="sort-icon">↕</span></th>
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
                        <div class="table-container">
                            <table class="data-table" id="usersTable">
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
                    <label for="studentFish">F.I.Sh *</label>
                    <input name="fish" type="text" id="studentFish" required>
                </div>
                <div class="form-group">
                    <label for="studentFakultet">Fakultet *</label>
                    <input name="fakultet" type="text" id="studentFakultet" required>
                </div>
                <div class="form-group">
                    <label for="studentGuruh">Guruh *</label>
                    <input name="guruh" type="text" id="studentGuruh" required>
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
                <div class="form-group">
                    <label for="studentManzil">Manzil</label>
                    <input manzil type="text" id="studentManzil">
                </div>
                <div class="form-group">
                    <label for="studentUrlManzil">Manzil URL</label>
                    <input name="url_manzil" type="url" id="studentUrlManzil"
                        placeholder="https://maps.google.com/...">
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
                    <input name="name" type="text" id="userName" required>
                </div>
                <div class="form-group">
                    <label for="userEmail">Email *</label>
                    <input name="email" type="email" id="userEmail" required>
                </div>
                <div class="form-group">
                    <label for="userChatId">Chat ID *</label>
                    <input name="chat_id" type="number" id="userChatId" required>
                </div>
                <div class="form-group">
                    <label for="userRole">Rol *</label>
                    <select name="role" id="userRole" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="userPassword">Parol *</label>
                    <input name="password" type="password" id="userPassword" required>
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
