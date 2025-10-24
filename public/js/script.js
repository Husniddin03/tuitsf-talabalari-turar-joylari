// Data management for Laravel integration
class DataManager {
    constructor() {
        // Laravel dan kelgan ma'lumotlarni olish
        this.students = this.initializeStudents();
        this.users = this.initializeUsers();
        this.currentStudentId = null;
        this.currentUserId = null;
        this.sortField = { students: "id", users: "id" };
        this.sortDirection = { students: "asc", users: "asc" };
    }

    // Laravel dan kelgan students ma'lumotlarini olish
    initializeStudents() {
        console.log("Initializing students...");
        console.log("window.mockStudents1:", window.mockStudents1);

        // // Avval localStorage dan tekshiramiz
        // const stored = localStorage.getItem('students');
        // if (stored) {
        //     const parsedStudents = JSON.parse(stored);
        //     console.log('Students from localStorage:', parsedStudents);
        //     return parsedStudents;
        // }

        // Laravel dan kelgan ma'lumotlarni ishlatamiz
        if (
            window.mockStudents1 &&
            Array.isArray(window.mockStudents1) &&
            window.mockStudents1.length > 0
        ) {
            console.log("Using Laravel students data:", window.mockStudents1);
            const laravelStudents = window.mockStudents1.map((student) => ({
                id: student.id,
                fish: student.fish || "",
                fakultet: student.fakultet || "",
                guruh: student.guruh || "",
                telefon: student.telefon || "",
                tyutori: student.tyutori || "",
                hudud: student.hudud || "",
                manzil: student.manzil || "",
                url_manzil: student.url_manzil || "",
                created_at: student.created_at,
                updated_at: student.updated_at,
            }));

            // LocalStorage ga saqlaymiz
            this.saveStudents(laravelStudents);
            return laravelStudents;
        }

        // Agar Laravel ma'lumotlari ham bo'lmasa, mock data ishlatamiz
        console.log("Using mock students data");
        const mockStudents = [
            {
                id: 1,
                fish: "Aliyev Vali Akramovich",
                fakultet: "Informatika",
                guruh: "IT-21",
                telefon: "+998901234567",
                tyutori: "Prof. Karimov",
                hudud: "Toshkent",
                manzil: "Chilonzor tumani",
                url_manzil: "https://maps.google.com/...",
                created_at: "2024-01-15T10:30:00Z",
                updated_at: "2024-01-15T10:30:00Z",
            },
            {
                id: 2,
                fish: "Karimova Malika Shavkatovna",
                fakultet: "Iqtisodiyot",
                guruh: "EK-22",
                telefon: "+998907654321",
                tyutori: "Dots. Rahimov",
                hudud: "Samarqand",
                manzil: "Registon ko'chasi",
                url_manzil: "",
                created_at: "2024-01-16T14:20:00Z",
                updated_at: "2024-01-16T14:20:00Z",
            },
        ];

        this.saveStudents(mockStudents);
        return mockStudents;
    }

    // Laravel dan kelgan users ma'lumotlarini olish
    initializeUsers() {
        console.log("Initializing users...");
        console.log("window.mockUsers1:", window.mockUsers1);

        // Avval localStorage dan tekshiramiz
        // const stored = localStorage.getItem('users');
        // if (stored) {
        //     const parsedUsers = JSON.parse(stored);
        //     console.log('Users from localStorage:', parsedUsers);
        //     return parsedUsers;
        // }

        // Laravel dan kelgan ma'lumotlarni ishlatamiz
        if (
            window.mockUsers1 &&
            Array.isArray(window.mockUsers1) &&
            window.mockUsers1.length > 0
        ) {
            console.log("Using Laravel users data:", window.mockUsers1);
            const laravelUsers = window.mockUsers1.map((user) => ({
                id: user.id,
                name: user.name || "",
                email: user.email || "",
                chat_id: user.chat_id || 0,
                role: user.role || "user",
                password: user.password || "",
                email_verified_at: user.email_verified_at || "",
                remember_token: user.remember_token || "",
                created_at: user.created_at,
                updated_at: user.updated_at,
            }));

            // LocalStorage ga saqlaymiz
            this.saveUsers(laravelUsers);
            return laravelUsers;
        }

        // Agar Laravel ma'lumotlari ham bo'lmasa, mock data ishlatamiz
        console.log("Using mock users data");
        const mockUsers = [
            {
                id: 1,
                name: "Admin User",
                email: "admin@example.com",
                chat_id: 123456789,
                role: "admin",
                password: "hashed_password",
                email_verified_at: "2024-01-01T00:00:00Z",
                remember_token: "",
                created_at: "2024-01-01T00:00:00Z",
                updated_at: "2024-01-01T00:00:00Z",
            },
            {
                id: 2,
                name: "Regular User",
                email: "user@example.com",
                chat_id: 987654321,
                role: "user",
                password: "hashed_password",
                email_verified_at: "",
                remember_token: "",
                created_at: "2024-01-02T00:00:00Z",
                updated_at: "2024-01-02T00:00:00Z",
            },
        ];

        this.saveUsers(mockUsers);
        return mockUsers;
    }

    saveStudents(students) {
        localStorage.setItem("students", JSON.stringify(students));
        this.students = students;
        console.log("Students saved to localStorage:", students);
    }

    async addStudent(studentData) {
        const newStudent = {
            ...studentData,
            id: Math.max(...this.students.map((s) => s.id), 0) + 1,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };

        const updatedStudents = [...this.students, newStudent];
        this.saveStudents(updatedStudents);

        // 🔽 FormData tayyorlash
        const formData = new FormData();
        for (const key in newStudent) {
            formData.append(key, newStudent[key]);
        }

        // CSRF tokenni olish
        const csrfToken = document.querySelector('input[name="_token"]')?.value;
        if (csrfToken) {
            formData.append("_token", csrfToken);
        }

        // 🔽 Laravel route URL (formdan olamiz)
        const formAction =
            document.getElementById("studentForm")?.action || "/web/store";

        try {
            const response = await fetch(formAction, {
                method: "POST",
                body: formData,
            });

            if (!response.ok) {
                throw new Error("Serverga yuborishda xatolik");
            }

            const result = await response.json(); // Agar server JSON qaytarsa
            console.log("Serverdan javob:", result);
        } catch (error) {
            console.error("Yuborishda xatolik:", error);
        }

        return newStudent;
    }

    async updateStudent(id, updates) {
        const index = this.students.findIndex((s) => s.id === id);
        if (index === -1) return null;

        const updatedStudent = {
            ...this.students[index],
            ...updates,
            updated_at: new Date().toISOString(),
        };

        const updatedStudents = [...this.students];
        updatedStudents[index] = updatedStudent;
        this.saveStudents(updatedStudents);

        // 🔽 FormData tayyorlash
        const formData = new FormData();
        for (const key in updatedStudent) {
            formData.append(key, updatedStudent[key]);
        }
        formData.append("_method", "PUT"); // Laravel uchun method spoofing

        const csrfToken = document.querySelector('input[name="_token"]')?.value;
        if (csrfToken) formData.append("_token", csrfToken);

        try {
            const response = await fetch(`/web/${id}`, {
                method: "POST", // Laravel PUT so‘rovini POST orqali qabul qiladi
                body: formData,
            });

            if (!response.ok) throw new Error("Yangilashda xatolik");
            const result = await response.json();
            console.log("Yangilangan student serverda:", result);
        } catch (error) {
            console.error("Serverga update yuborishda xatolik:", error);
        }

        return updatedStudent;
    }

    async deleteStudent(id) {
        const filteredStudents = this.students.filter((s) => s.id !== id);
        if (filteredStudents.length === this.students.length) return false;

        this.saveStudents(filteredStudents);

        const formData = new FormData();
        formData.append("_method", "DELETE"); // Laravel uchun method spoofing

        const csrfToken = document.querySelector('input[name="_token"]')?.value;
        if (csrfToken) formData.append("_token", csrfToken);

        try {
            const response = await fetch(`/web/${id}`, {
                method: "POST", // Laravel DELETE so‘rovini POST orqali qabul qiladi
                body: formData,
            });

            if (!response.ok) throw new Error("O‘chirishda xatolik");
            const result = await response.json();
            console.log("Serverdan o‘chirish javobi:", result);
        } catch (error) {
            console.error("Serverga delete yuborishda xatolik:", error);
        }

        return true;
    }

    saveUsers(users) {
        localStorage.setItem("users", JSON.stringify(users));
        this.users = users;
        console.log("Users saved to localStorage:", users);
    }

    async addUser(userData) {
        const newUser = {
            ...userData,
            id: Math.max(...this.users.map((u) => u.id), 0) + 1,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };

        const updatedUsers = [...this.users, newUser];
        this.saveUsers(updatedUsers);

        const formData = new FormData();
        for (const key in newUser) {
            formData.append(key, newUser[key]);
        }

        const csrfToken = document.querySelector('input[name="_token"]')?.value;
        if (csrfToken) formData.append("_token", csrfToken);

        try {
            const response = await fetch("/admin", {
                method: "POST",
                body: formData,
            });

            if (!response.ok)
                throw new Error("Foydalanuvchi qo‘shishda xatolik");
            const result = await response.json();
            console.log("Serverga qo‘shildi:", result);
        } catch (error) {
            console.error("Serverga addUser yuborishda xatolik:", error);
        }

        return newUser;
    }

    async updateUser(id, updates) {
        const index = this.users.findIndex((u) => u.id === id);
        if (index === -1) return null;

        const updatedUser = {
            ...this.users[index],
            ...updates,
            updated_at: new Date().toISOString(),
        };

        const updatedUsers = [...this.users];
        updatedUsers[index] = updatedUser;
        this.saveUsers(updatedUsers);

        const formData = new FormData();
        for (const key in updatedUser) {
            formData.append(key, updatedUser[key]);
        }
        formData.append("_method", "PUT");

        const csrfToken = document.querySelector('input[name="_token"]')?.value;
        if (csrfToken) formData.append("_token", csrfToken);

        try {
            const response = await fetch(`/admin/${id}`, {
                method: "POST",
                body: formData,
            });

            if (!response.ok)
                throw new Error("Foydalanuvchini yangilashda xatolik");
            const result = await response.json();
            console.log("Serverda yangilandi:", result);
        } catch (error) {
            console.error("Serverga updateUser yuborishda xatolik:", error);
        }

        return updatedUser;
    }

    async deleteUser(id) {
        const filteredUsers = this.users.filter((u) => u.id !== id);
        if (filteredUsers.length === this.users.length) return false;

        this.saveUsers(filteredUsers);

        const formData = new FormData();
        formData.append("_method", "DELETE");

        const csrfToken = document.querySelector('input[name="_token"]')?.value;
        if (csrfToken) formData.append("_token", csrfToken);

        try {
            const response = await fetch(`/admin/${id}`, {
                method: "POST",
                body: formData,
            });

            if (!response.ok)
                throw new Error("Foydalanuvchini o‘chirishda xatolik");
            const result = await response.json();
            console.log("Serverdan o‘chirildi:", result);
        } catch (error) {
            console.error("Serverga deleteUser yuborishda xatolik:", error);
        }

        return true;
    }

    // Statistics
    getStats() {
        const faculties = [
            ...new Set(this.students.map((s) => s.fakultet).filter((f) => f)),
        ];
        const admins = this.users.filter((u) => u.role === "admin");

        return {
            totalStudents: this.students.length,
            totalUsers: this.users.length,
            totalFaculties: faculties.length,
            totalAdmins: admins.length,
        };
    }
}

// UI Manager
class UIManager {
    constructor(dataManager) {
        this.dataManager = dataManager;
        this.init();
    }

    init() {
        console.log("Initializing UI...");
        console.log("Students count:", this.dataManager.students.length);
        console.log("Users count:", this.dataManager.users.length);

        this.setupEventListeners();
        this.updateStats();
        this.renderStudentsTable();
        this.renderUsersTable();
    }

    setupEventListeners() {
        // Tab switching
        document.querySelectorAll(".tab-btn").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                const tab = e.target.closest(".tab-btn").dataset.tab;
                this.switchTab(tab);
            });
        });

        // Search functionality
        const studentSearch = document.getElementById("studentSearch");
        if (studentSearch) {
            studentSearch.addEventListener("input", (e) => {
                this.renderStudentsTable(e.target.value);
            });
        }

        const userSearch = document.getElementById("userSearch");
        if (userSearch) {
            userSearch.addEventListener("input", (e) => {
                this.renderUsersTable(e.target.value);
            });
        }

        // Add buttons
        const addStudentBtn = document.getElementById("addStudentBtn");
        if (addStudentBtn) {
            addStudentBtn.addEventListener("click", () => {
                this.openStudentModal();
            });
        }

        const addUserBtn = document.getElementById("addUserBtn");
        if (addUserBtn) {
            addUserBtn.addEventListener("click", () => {
                this.openUserModal();
            });
        }

        // Modal close buttons
        const closeStudentModal = document.getElementById("closeStudentModal");
        if (closeStudentModal) {
            closeStudentModal.addEventListener("click", () => {
                this.closeStudentModal();
            });
        }

        const closeUserModal = document.getElementById("closeUserModal");
        if (closeUserModal) {
            closeUserModal.addEventListener("click", () => {
                this.closeUserModal();
            });
        }

        const cancelStudentBtn = document.getElementById("cancelStudentBtn");
        if (cancelStudentBtn) {
            cancelStudentBtn.addEventListener("click", () => {
                this.closeStudentModal();
            });
        }

        const cancelUserBtn = document.getElementById("cancelUserBtn");
        if (cancelUserBtn) {
            cancelUserBtn.addEventListener("click", () => {
                this.closeUserModal();
            });
        }

        // Form submissions
        const studentForm = document.getElementById("studentForm");
        if (studentForm) {
            studentForm.addEventListener("submit", (e) => {
                e.preventDefault();
                this.handleStudentSubmit();
            });
        }

        const userForm = document.getElementById("userForm");
        if (userForm) {
            userForm.addEventListener("submit", (e) => {
                e.preventDefault();
                this.handleUserSubmit();
            });
        }

        // Modal backdrop click
        const studentModal = document.getElementById("studentModal");
        if (studentModal) {
            studentModal.addEventListener("click", (e) => {
                if (e.target === e.currentTarget) {
                    this.closeStudentModal();
                }
            });
        }

        const userModal = document.getElementById("userModal");
        if (userModal) {
            userModal.addEventListener("click", (e) => {
                if (e.target === e.currentTarget) {
                    this.closeUserModal();
                }
            });
        }

        // Table sorting
        document
            .querySelectorAll("#studentsTable th[data-sort]")
            .forEach((th) => {
                th.addEventListener("click", () => {
                    this.sortStudentsTable(th.dataset.sort);
                });
            });

        document.querySelectorAll("#usersTable th[data-sort]").forEach((th) => {
            th.addEventListener("click", () => {
                this.sortUsersTable(th.dataset.sort);
            });
        });
    }

    switchTab(tab) {
        // Update tab buttons
        document.querySelectorAll(".tab-btn").forEach((btn) => {
            btn.classList.remove("active");
        });
        const activeTabBtn = document.querySelector(`[data-tab="${tab}"]`);
        if (activeTabBtn) {
            activeTabBtn.classList.add("active");
        }

        // Update tab content
        document.querySelectorAll(".tab-content").forEach((content) => {
            content.classList.remove("active");
        });
        const activeTabContent = document.getElementById(`${tab}-tab`);
        if (activeTabContent) {
            activeTabContent.classList.add("active");
        }
    }

    updateStats() {
        const stats = this.dataManager.getStats();

        const totalStudentsEl = document.getElementById("totalStudents");
        if (totalStudentsEl) totalStudentsEl.textContent = stats.totalStudents;

        const totalUsersEl = document.getElementById("totalUsers");
        if (totalUsersEl) totalUsersEl.textContent = stats.totalUsers;

        const totalFacultiesEl = document.getElementById("totalFaculties");
        if (totalFacultiesEl)
            totalFacultiesEl.textContent = stats.totalFaculties;

        const totalAdminsEl = document.getElementById("totalAdmins");
        if (totalAdminsEl) totalAdminsEl.textContent = stats.totalAdmins;
    }

    // Students table
    renderStudentsTable(searchTerm = "") {
        console.log("Rendering students table...");
        console.log("Students data:", this.dataManager.students);

        const tbody = document.getElementById("studentsTableBody");
        const countElement = document.getElementById("studentsCount");

        if (!tbody) {
            console.error("studentsTableBody element not found");
            return;
        }

        let students = [...this.dataManager.students];
        console.log("Students before filter:", students);

        // Filter by search term
        if (searchTerm) {
            students = students.filter((student) =>
                Object.values(student).some(
                    (value) =>
                        value &&
                        value
                            .toString()
                            .toLowerCase()
                            .includes(searchTerm.toLowerCase())
                )
            );
        }

        // Sort
        const sortField = this.dataManager.sortField.students;
        const sortDirection = this.dataManager.sortDirection.students;

        students.sort((a, b) => {
            const aValue = a[sortField]?.toString() || "";
            const bValue = b[sortField]?.toString() || "";

            if (sortDirection === "asc") {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });

        console.log("Students after filter and sort:", students);

        // Render rows
        if (students.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="empty-state">Talabalar topilmadi</td>
                </tr>
            `;
        } else {
            tbody.innerHTML = students
                .map(
                    (student) => `
                <tr>
                    <td>${student.id}</td>
                    <td><strong>${student.fish || "-"}</strong></td>
                    <td><span class="badge badge-secondary">${
                        student.fakultet || "-"
                    }</span></td>
                    <td>${student.guruh || "-"}</td>
                    <td>${student.telefon || "-"}</td>
                    <td>${student.tyutori || "-"}</td>
                    <td>${student.hudud || "-"}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon" onclick="ui.editStudent(${
                                student.id
                            })">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            <button class="btn-icon danger" onclick="ui.deleteStudent(${
                                student.id
                            })">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3,6 5,6 21,6"/>
                                    <path d="M19,6v14a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6m3,0V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `
                )
                .join("");
        }

        if (countElement) {
            countElement.textContent = `Jami: ${students.length} ta talaba`;
        }

        this.updateSortIcons("students");
    }

    sortStudentsTable(field) {
        if (this.dataManager.sortField.students === field) {
            this.dataManager.sortDirection.students =
                this.dataManager.sortDirection.students === "asc"
                    ? "desc"
                    : "asc";
        } else {
            this.dataManager.sortField.students = field;
            this.dataManager.sortDirection.students = "asc";
        }

        const searchTerm =
            document.getElementById("studentSearch")?.value || "";
        this.renderStudentsTable(searchTerm);
    }

    updateSortIcons(table) {
        const tableElement =
            table === "students" ? "studentsTable" : "usersTable";
        const sortField = this.dataManager.sortField[table];
        const sortDirection = this.dataManager.sortDirection[table];

        // Reset all sort icons
        document.querySelectorAll(`#${tableElement} th`).forEach((th) => {
            th.classList.remove("sort-asc", "sort-desc");
        });

        // Set active sort icon
        const activeHeader = document.querySelector(
            `#${tableElement} th[data-sort="${sortField}"]`
        );
        if (activeHeader) {
            activeHeader.classList.add(`sort-${sortDirection}`);
        }
    }

    // Users table
    renderUsersTable(searchTerm = "") {
        console.log("Rendering users table...");
        console.log("Users data:", this.dataManager.users);

        const tbody = document.getElementById("usersTableBody");
        const countElement = document.getElementById("usersCount");

        if (!tbody) {
            console.error("usersTableBody element not found");
            return;
        }

        let users = [...this.dataManager.users];

        // Filter by search term
        if (searchTerm) {
            users = users.filter((user) =>
                Object.values(user).some(
                    (value) =>
                        value &&
                        value
                            .toString()
                            .toLowerCase()
                            .includes(searchTerm.toLowerCase())
                )
            );
        }

        // Sort
        const sortField = this.dataManager.sortField.users;
        const sortDirection = this.dataManager.sortDirection.users;

        users.sort((a, b) => {
            const aValue = a[sortField]?.toString() || "";
            const bValue = b[sortField]?.toString() || "";

            if (sortDirection === "asc") {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });

        // Render rows
        if (users.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="empty-state">Foydalanuvchilar topilmadi</td>
                </tr>
            `;
        } else {
            tbody.innerHTML = users
                .map(
                    (user) => `
                <tr>
                    <td>${user.id}</td>
                    <td><strong>${user.name || "-"}</strong></td>
                    <td>${user.email || "-"}</td>
                    <td>${user.chat_id || "-"}</td>
                    <td><span class="badge ${this.getRoleBadgeClass(
                        user.role
                    )}">${user.role || "user"}</span></td>
                    <td>${this.formatDate(user.created_at)}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon" onclick="ui.editUser(${
                                user.id
                            })">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            <button class="btn-icon danger" onclick="ui.deleteUser(${
                                user.id
                            })">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3,6 5,6 21,6"/>
                                    <path d="M19,6v14a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6m3,0V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `
                )
                .join("");
        }

        if (countElement) {
            countElement.textContent = `Jami: ${users.length} ta foydalanuvchi`;
        }

        this.updateSortIcons("users");
    }

    // Laravel sanalarini formatlash
    formatDate(dateString) {
        if (!dateString) return "-";

        try {
            const date = new Date(dateString);
            return date.toLocaleDateString("uz-UZ");
        } catch (error) {
            return dateString;
        }
    }

    sortUsersTable(field) {
        if (this.dataManager.sortField.users === field) {
            this.dataManager.sortDirection.users =
                this.dataManager.sortDirection.users === "asc" ? "desc" : "asc";
        } else {
            this.dataManager.sortField.users = field;
            this.dataManager.sortDirection.users = "asc";
        }

        const searchTerm = document.getElementById("userSearch")?.value || "";
        this.renderUsersTable(searchTerm);
    }

    getRoleBadgeClass(role) {
        switch (role) {
            case "admin":
                return "badge-destructive";
            case "moderator":
                return "badge-default";
            default:
                return "badge-secondary";
        }
    }

    // Student modal
    openStudentModal(student = null) {
        const modal = document.getElementById("studentModal");
        const title = document.getElementById("studentModalTitle");
        const form = document.getElementById("studentForm");

        if (!modal || !title || !form) return;

        this.dataManager.currentStudentId = student ? student.id : null;

        if (student) {
            title.textContent = "Talabani tahrirlash";
            document.getElementById("studentFish").value = student.fish || "";
            document.getElementById("studentFakultet").value =
                student.fakultet || "";
            document.getElementById("studentGuruh").value = student.guruh || "";
            document.getElementById("studentTelefon").value =
                student.telefon || "";
            document.getElementById("studentTyutori").value =
                student.tyutori || "";
            document.getElementById("studentHudud").value = student.hudud || "";
            document.getElementById("studentManzil").value =
                student.manzil || "";
            document.getElementById("studentUrlManzil").value =
                student.url_manzil || "";
            document.getElementById("saveStudentBtn").textContent = "Saqlash";
        } else {
            title.textContent = "Yangi talaba qo'shish";
            form.reset();
            document.getElementById("saveStudentBtn").textContent = "Qo'shish";
        }

        modal.classList.add("active");
    }

    closeStudentModal() {
        const modal = document.getElementById("studentModal");
        if (modal) {
            modal.classList.remove("active");
        }
        this.dataManager.currentStudentId = null;
    }

    handleStudentSubmit() {
        const studentData = {
            fish: document.getElementById("studentFish")?.value || "",
            fakultet: document.getElementById("studentFakultet")?.value || "",
            guruh: document.getElementById("studentGuruh")?.value || "",
            telefon: document.getElementById("studentTelefon")?.value || "",
            tyutori: document.getElementById("studentTyutori")?.value || "",
            hudud: document.getElementById("studentHudud")?.value || "",
            manzil: document.getElementById("studentManzil")?.value || "",
            url_manzil:
                document.getElementById("studentUrlManzil")?.value || "",
        };

        if (this.dataManager.currentStudentId) {
            this.dataManager.updateStudent(
                this.dataManager.currentStudentId,
                studentData
            );
        } else {
            this.dataManager.addStudent(studentData);
        }

        this.closeStudentModal();
        this.renderStudentsTable();
        this.updateStats();
    }

    editStudent(id) {
        const student = this.dataManager.students.find((s) => s.id === id);
        if (student) {
            this.openStudentModal(student);
        }
    }

    deleteStudent(id) {
        if (confirm("Talabani o'chirishga ishonchingiz komilmi?")) {
            if (this.dataManager.deleteStudent(id)) {
                this.renderStudentsTable();
                this.updateStats();
            }
        }
    }

    // User modal
    openUserModal(user = null) {
        const modal = document.getElementById("userModal");
        const title = document.getElementById("userModalTitle");
        const form = document.getElementById("userForm");

        if (!modal || !title || !form) return;

        this.dataManager.currentUserId = user ? user.id : null;

        if (user) {
            title.textContent = "Foydalanuvchini tahrirlash";
            document.getElementById("userName").value = user.name || "";
            document.getElementById("userEmail").value = user.email || "";
            document.getElementById("userChatId").value = user.chat_id || "";
            document.getElementById("userRole").value = user.role || "user";
            document.getElementById("userPassword").value = user.password || "";

            // Laravel dan kelgan sana formatini to'g'rilash
            if (user.email_verified_at) {
                try {
                    const date = new Date(user.email_verified_at);
                    document.getElementById("userEmailVerified").value = date
                        .toISOString()
                        .slice(0, 16);
                } catch (error) {
                    document.getElementById("userEmailVerified").value = "";
                }
            } else {
                document.getElementById("userEmailVerified").value = "";
            }

            document.getElementById("saveUserBtn").textContent = "Saqlash";
        } else {
            title.textContent = "Yangi foydalanuvchi qo'shish";
            form.reset();
            document.getElementById("saveUserBtn").textContent = "Qo'shish";
        }

        modal.classList.add("active");
    }

    closeUserModal() {
        const modal = document.getElementById("userModal");
        if (modal) {
            modal.classList.remove("active");
        }
        this.dataManager.currentUserId = null;
    }

    handleUserSubmit() {
        const userData = {
            name: document.getElementById("userName")?.value || "",
            email: document.getElementById("userEmail")?.value || "",
            chat_id: parseInt(
                document.getElementById("userChatId")?.value || "0"
            ),
            role: document.getElementById("userRole")?.value || "user",
            password: document.getElementById("userPassword")?.value || "",
            email_verified_at:
                document.getElementById("userEmailVerified")?.value || null,
            remember_token: null,
        };

        if (this.dataManager.currentUserId) {
            this.dataManager.updateUser(
                this.dataManager.currentUserId,
                userData
            );
        } else {
            this.dataManager.addUser(userData);
        }

        this.closeUserModal();
        this.renderUsersTable();
        this.updateStats();
    }

    editUser(id) {
        const user = this.dataManager.users.find((u) => u.id === id);
        if (user) {
            this.openUserModal(user);
        }
    }

    deleteUser(id) {
        if (confirm("Foydalanuvchini o'chirishga ishonchingiz komilmi?")) {
            if (this.dataManager.deleteUser(id)) {
                this.renderUsersTable();
                this.updateStats();
            }
        }
    }
}

// DOM yuklangandan keyin yoki Laravel ma'lumotlari tayyor bo'lganda ishga tushirish
function initializeApp() {
    console.log("Initializing app...");
    console.log("Laravel ma'lumotlari:", {
        students: window.mockStudents1 ? window.mockStudents1.length : 0,
        users: window.mockUsers1 ? window.mockUsers1.length : 0,
    });

    // Initialize the application
    const dataManager = new DataManager();
    const ui = new UIManager(dataManager);

    // Make ui globally available for onclick handlers
    window.ui = ui;
}

// DOM yuklangandan keyin ishga tushirish
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeApp);
} else {
    // Agar DOM allaqachon yuklangan bo'lsa
    initializeApp();
}

// Laravel ma'lumotlari keyinroq yuklansa ham ishlashi uchun
window.addEventListener("load", function () {
    // Agar app hali ishga tushmagan bo'lsa
    if (!window.ui) {
        setTimeout(initializeApp, 100);
    }
});
