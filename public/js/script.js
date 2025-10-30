// Role-based permissions
class PermissionManager {
    constructor() {
        this.currentUserRole = window.currentUserRole || "user";
        this.permissions = {
            user: {
                students: {
                    view: false,
                    create: false,
                    edit: false,
                    delete: false,
                },
                users: {
                    view: false,
                    create: false,
                    edit: false,
                    delete: false,
                },
                ownProfile: { view: true, edit: true },
            },
            admin: {
                students: {
                    view: true,
                    create: true,
                    edit: true,
                    delete: true,
                },
                users: {
                    view: false,
                    create: false,
                    edit: false,
                    delete: false,
                },
                ownProfile: { view: true, edit: true },
            },
            super_admin: {
                students: {
                    view: true,
                    create: true,
                    edit: true,
                    delete: true,
                },
                users: { view: true, create: true, edit: true, delete: true },
                ownProfile: { view: true, edit: true },
            },
        };
    }

    canAccess(resource, action) {
        const rolePermissions = this.permissions[this.currentUserRole];
        if (!rolePermissions || !rolePermissions[resource]) {
            return false;
        }
        return rolePermissions[resource][action] || false;
    }

    filterUsersByRole(users) {
        if (this.currentUserRole === "super_admin") {
            return users; // Super admin can see all users
        }
        if (this.currentUserRole === "admin") {
            return users.filter(
                (user) => user.role !== "admin" && user.role !== "super_admin"
            );
        }
        return []; // Regular users can't see other users
    }
}

// Enhanced search with debouncing
class SearchManager {
    constructor() {
        this.searchTimeouts = {};
        this.searchDelay = 500; // 0.5 seconds
    }

    debounceSearch(searchId, callback, query) {
        // Clear existing timeout
        if (this.searchTimeouts[searchId]) {
            clearTimeout(this.searchTimeouts[searchId]);
        }

        // Show loading indicator
        const loadingEl = document.getElementById(`${searchId}Loading`);
        if (loadingEl) {
            loadingEl.style.display = "block";
        }

        // Set new timeout
        this.searchTimeouts[searchId] = setTimeout(() => {
            callback(query);
            // Hide loading indicator
            if (loadingEl) {
                loadingEl.style.display = "none";
            }
        }, this.searchDelay);
    }

    // Optimized search - only search in key fields
    searchStudents(students, query) {
        if (!query.trim()) return students;

        const searchTerm = query.toLowerCase().trim();
        const searchFields = ["id", "talaba_id", "fish", "fakultet", "guruh"]; // Limited fields for faster search

        return students.filter((student) => {
            return searchFields.some((field) => {
                const value = student[field];
                return (
                    value && value.toString().toLowerCase().includes(searchTerm)
                );
            });
        });
    }

    searchUsers(users, query) {
        if (!query.trim()) return users;

        const searchTerm = query.toLowerCase().trim();
        const searchFields = ["id", "name", "email", "role"]; // Limited fields for faster search

        return users.filter((user) => {
            return searchFields.some((field) => {
                const value = user[field];
                return (
                    value && value.toString().toLowerCase().includes(searchTerm)
                );
            });
        });
    }
}

// Pagination manager
class PaginationManager {
    constructor() {
        this.itemsPerPage = 50;
        this.currentPages = {
            students: 1,
            users: 1,
        };
    }

    paginate(items, page) {
        const startIndex = (page - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        return items.slice(startIndex, endIndex);
    }

    getTotalPages(totalItems) {
        return Math.ceil(totalItems / this.itemsPerPage);
    }

    setCurrentPage(type, page) {
        this.currentPages[type] = page;
    }

    getCurrentPage(type) {
        return this.currentPages[type];
    }

    renderPagination(type, totalItems, onPageChange) {
        const totalPages = this.getTotalPages(totalItems);
        const currentPage = this.getCurrentPage(type);

        // Update page info
        const pageInfoEl = document.getElementById(`${type}PageInfo`);
        if (pageInfoEl) {
            pageInfoEl.textContent = `Sahifa ${currentPage} / ${totalPages}`;
        }

        // Update navigation buttons
        const prevBtn = document.getElementById(`${type}PrevBtn`);
        const nextBtn = document.getElementById(`${type}NextBtn`);

        if (prevBtn) {
            prevBtn.disabled = currentPage <= 1;
            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    this.setCurrentPage(type, currentPage - 1);
                    onPageChange();
                }
            };
        }

        if (nextBtn) {
            nextBtn.disabled = currentPage >= totalPages;
            nextBtn.onclick = () => {
                if (currentPage < totalPages) {
                    this.setCurrentPage(type, currentPage + 1);
                    onPageChange();
                }
            };
        }

        // Render page numbers
        const paginationEl = document.getElementById(`${type}Pagination`);
        if (paginationEl) {
            paginationEl.innerHTML = "";

            const maxVisiblePages = 5;
            let startPage = Math.max(
                1,
                currentPage - Math.floor(maxVisiblePages / 2)
            );
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            // Adjust start page if we're near the end
            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement("button");
                pageBtn.className = `pagination-page ${
                    i === currentPage ? "active" : ""
                }`;
                pageBtn.textContent = i;
                pageBtn.onclick = () => {
                    this.setCurrentPage(type, i);
                    onPageChange();
                };
                paginationEl.appendChild(pageBtn);
            }
        }
    }
}

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

        // Initialize managers
        this.permissionManager = new PermissionManager();
        this.searchManager = new SearchManager();
        this.paginationManager = new PaginationManager();
    }

    // Laravel dan kelgan students ma'lumotlarini olish
    initializeStudents() {
        console.log("Initializing students...");
        console.log("window.mockStudents1:", window.mockStudents1);

        // Laravel dan kelgan ma'lumotlarni ishlatamiz
        if (
            window.mockStudents1 &&
            Array.isArray(window.mockStudents1) &&
            window.mockStudents1.length > 0
        ) {
            console.log("Using Laravel students data:", window.mockStudents1);
            const laravelStudents = window.mockStudents1.map((student) => ({
                id: student.id,
                talaba_id: student.talaba_id,
                fish: student.fish || "",
                fakultet: student.fakultet || "",
                guruh: student.guruh || "",
                telefon: student.telefon || "",
                tyutori: student.tyutori || "",
                hudud: student.hudud || "",
                doimiy_yashash_viloyati: student.doimiy_yashash_viloyati || "",
                doimiy_yashash_tumani: student.doimiy_yashash_tumani || "",
                doimiy_yashash_manzili: student.doimiy_yashash_manzili || "",
                doimiy_yashash_manzili_urli:
                    student.doimiy_yashash_manzili_urli || "",
                vaqtincha_yashash_viloyati:
                    student.vaqtincha_yashash_viloyati || "",
                vaqtincha_yashash_tumani:
                    student.vaqtincha_yashash_tumani || "",
                vaqtincha_yashash_manzili:
                    student.vaqtincha_yashash_manzili || "",
                vaqtincha_yashash_manzili_urli:
                    student.vaqtincha_yashash_manzili_urli || "",
                uy_egasi: student.uy_egasi || "",
                uy_egasi_telefoni: student.uy_egasi_telefoni || "",
                yotoqxona_nomeri: student.yotoqxona_nomeri || "",
                narx: student.narx || "",
                ota_ona: student.ota_ona || "",
                ota_ona_telefoni: student.ota_ona_telefoni || "",
                created_at: student.created_at,
                updated_at: student.updated_at,
            }));

            // LocalStorage ga saqlaymiz
            this.saveStudents(laravelStudents);
            return laravelStudents;
        }

        // Agar Laravel ma'lumotlari ham bo'lmasa, mock data ishlatamiz
        console.log("Using mock students data");
        const mockStudents = [];

        this.saveStudents(mockStudents);
        return mockStudents;
    }

    // Laravel dan kelgan users ma'lumotlarini olish
    initializeUsers() {
        console.log("Initializing users...");
        console.log("window.mockUsers1:", window.mockUsers1);

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
        const mockUsers = [];

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
                method: "POST", // Laravel PUT so'rovini POST orqali qabul qiladi
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
                method: "POST", // Laravel DELETE so'rovini POST orqali qabul qiladi
                body: formData,
            });

            if (!response.ok) throw new Error("O'chirishda xatolik");
            const result = await response.json();
            console.log("Serverdan o'chirish javobi:", result);
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
                throw new Error("Foydalanuvchi qo'shishda xatolik");
            const result = await response.json();
            console.log("Serverga qo'shildi:", result);
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
                throw new Error("Foydalanuvchini o'chirishda xatolik");
            const result = await response.json();
            console.log("Serverdan o'chirildi:", result);
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

        // Filter users based on permissions
        const visibleUsers = this.permissionManager.filterUsersByRole(
            this.users
        );
        const admins = visibleUsers.filter(
            (u) => u.role === "admin" || u.role === "super_admin"
        );

        return {
            totalStudents: this.students.length,
            totalUsers: visibleUsers.length,
            totalFaculties: faculties.length,
            totalAdmins: admins.length,
        };
    }
}

// UI Manager
class UIManager {
    constructor(dataManager) {
        this.dataManager = dataManager;
        this.currentSearchQueries = {
            students: "",
            users: "",
        };
        this.init();
    }

    init() {
        console.log("Initializing UI...");
        console.log("Students count:", this.dataManager.students.length);
        console.log("Users count:", this.dataManager.users.length);
        console.log(
            "Current user role:",
            this.dataManager.permissionManager.currentUserRole
        );

        this.setupEventListeners();
        this.updateStats();
        this.checkPermissions();
        this.renderStudentsTable();
        this.renderUsersTable();
    }

    checkPermissions() {
        const permissionManager = this.dataManager.permissionManager;

        // Check students tab access
        const studentsTab = document.getElementById("studentsTab");
        if (!permissionManager.canAccess("students", "view")) {
            if (studentsTab) studentsTab.style.display = "none";
        }

        // Check users tab access
        const usersTab = document.getElementById("usersTab");
        if (!permissionManager.canAccess("users", "view")) {
            if (usersTab) usersTab.style.display = "none";
        }

        // Check add buttons
        const addStudentBtn = document.getElementById("addStudentBtn");
        if (
            addStudentBtn &&
            !permissionManager.canAccess("students", "create")
        ) {
            addStudentBtn.style.display = "none";
        }

        const addUserBtn = document.getElementById("addUserBtn");
        if (addUserBtn && !permissionManager.canAccess("users", "create")) {
            addUserBtn.style.display = "none";
        }
    }

    setupEventListeners() {
        // Tab switching
        document.querySelectorAll(".tab-btn").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                const tab = e.target.closest(".tab-btn").dataset.tab;
                if (tab) {
                    this.switchTab(tab);
                }
            });
        });

        // Enhanced search functionality with debouncing
        const studentSearch = document.getElementById("studentSearch");
        if (studentSearch) {
            studentSearch.addEventListener("input", (e) => {
                this.currentSearchQueries.students = e.target.value;
                this.dataManager.searchManager.debounceSearch(
                    "studentSearch",
                    () => {
                        this.dataManager.paginationManager.setCurrentPage(
                            "students",
                            1
                        );
                        this.renderStudentsTable();
                    },
                    e.target.value
                );
            });
        }

        const userSearch = document.getElementById("userSearch");
        if (userSearch) {
            userSearch.addEventListener("input", (e) => {
                this.currentSearchQueries.users = e.target.value;
                this.dataManager.searchManager.debounceSearch(
                    "userSearch",
                    () => {
                        this.dataManager.paginationManager.setCurrentPage(
                            "users",
                            1
                        );
                        this.renderUsersTable();
                    },
                    e.target.value
                );
            });
        }

        // Add buttons
        const addStudentBtn = document.getElementById("addStudentBtn");
        if (addStudentBtn) {
            addStudentBtn.addEventListener("click", () => {
                if (
                    this.dataManager.permissionManager.canAccess(
                        "students",
                        "create"
                    )
                ) {
                    this.openStudentModal();
                }
            });
        }

        const addUserBtn = document.getElementById("addUserBtn");
        if (addUserBtn) {
            addUserBtn.addEventListener("click", () => {
                if (
                    this.dataManager.permissionManager.canAccess(
                        "users",
                        "create"
                    )
                ) {
                    this.openUserModal();
                }
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

    // Students table with pagination
    renderStudentsTable() {
        console.log("Rendering students table...");

        const tbody = document.getElementById("studentsTableBody");
        const countElement = document.getElementById("studentsCount");

        if (!tbody) {
            console.error("studentsTableBody element not found");
            return;
        }

        // Check permissions
        if (!this.dataManager.permissionManager.canAccess("students", "view")) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="permission-denied">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                        <h3>Ruxsat berilmagan</h3>
                        <p>Sizda talabalar ma'lumotlarini ko'rish huquqi yo'q</p>
                    </td>
                </tr>
            `;
            return;
        }

        let students = [...this.dataManager.students];

        // Apply search filter
        const searchQuery = this.currentSearchQueries.students;
        if (searchQuery) {
            students = this.dataManager.searchManager.searchStudents(
                students,
                searchQuery
            );
        }

        // Sort
        const sortField = this.dataManager.sortField.students;
        const sortDirection = this.dataManager.sortDirection.students;

        students.sort((a, b) => {
            let aValue = a[sortField];
            let bValue = b[sortField];

            // Agar qiymatlar son bo'lsa — raqam sifatida solishtiramiz
            const isNumeric = !isNaN(parseFloat(aValue)) && isFinite(aValue);

            if (isNumeric) {
                aValue = parseFloat(aValue);
                bValue = parseFloat(bValue);
                return sortDirection === "asc"
                    ? aValue - bValue
                    : bValue - aValue;
            }

            // Aks holda — matn sifatida solishtiramiz
            aValue = String(aValue).toLowerCase();
            bValue = String(bValue).toLowerCase();

            return sortDirection === "asc"
                ? aValue.localeCompare(bValue)
                : bValue.localeCompare(aValue);
        });

        // Apply pagination
        const currentPage =
            this.dataManager.paginationManager.getCurrentPage("students");
        const paginatedStudents = this.dataManager.paginationManager.paginate(
            students,
            currentPage
        );

        // Render pagination
        this.dataManager.paginationManager.renderPagination(
            "students",
            students.length,
            () => {
                this.renderStudentsTable();
            }
        );

        // Render rows
        if (paginatedStudents.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="empty-state">
                        ${
                            searchQuery
                                ? "Qidiruv bo'yicha talabalar topilmadi"
                                : "Talabalar topilmadi"
                        }
                    </td>
                </tr>
            `;
        } else {
            const canEdit = this.dataManager.permissionManager.canAccess(
                "students",
                "edit"
            );
            const canDelete = this.dataManager.permissionManager.canAccess(
                "students",
                "delete"
            );

            tbody.innerHTML = paginatedStudents
                .map(
                    (student) => `
                <tr>
                   <td>${student.id}</td>
                        <td><strong>${student.talaba_id || "-"}</strong></td>
                    <td><strong>${student.fish || "-"}</strong></td>
                    <td><span class="badge badge-secondary">${
                        student.fakultet || "-"
                    }</span></td>
                    <td>${student.guruh || "-"}</td>
                    <td>${student.telefon || "-"}</td>
                    <td>${student.tyutori || "-"}</td>
                    <td>${student.hudud || "-"}</td>
                    <td>${student.doimiy_yashash_viloyati || "-"}</td>
                    <td>${student.doimiy_yashash_tumani || "-"}</td>
                    <td>${student.doimiy_yashash_manzili || "-"}</td>
                    <td><a href="${
                        student.doimiy_yashash_manzili_urli || "#"
                    }" target="_blank">
                        ${
                            student.doimiy_yashash_manzili_urli
                                ? "Xaritadan ko'rish"
                                : "-"
                        }
                    </a></td>
                    <td>${student.vaqtincha_yashash_viloyati || "-"}</td>
                    <td>${student.vaqtincha_yashash_tumani || "-"}</td>
                    <td>${student.vaqtincha_yashash_manzili || "-"}</td>
                    <td><a href="${
                        student.vaqtincha_yashash_manzili_urli || "#"
                    }" target="_blank">
                        ${
                            student.vaqtincha_yashash_manzili_urli
                                ? "Xaritadan ko'rish"
                                : "-"
                        }
                    </a></td>
                    <td>${student.uy_egasi || "-"}</td>
                    <td>${student.uy_egasi_telefoni || "-"}</td>
                    <td>${student.yotoqxona_nomeri || "-"}</td>
                    <td>${student.narx || "-"}</td>
                    <td>${student.ota_ona || "-"}</td>
                    <td>${student.ota_ona_telefoni || "-"}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon" onclick="ui.editStudent(${
                                student.id
                            })">✏️</button>
                            <button class="btn-icon danger" onclick="ui.deleteStudent(${
                                student.id
                            })">🗑️</button>
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

        this.renderStudentsTable();
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

    // Users table with pagination and role filtering
    renderUsersTable() {
        console.log("Rendering users table...");

        const tbody = document.getElementById("usersTableBody");
        const countElement = document.getElementById("usersCount");

        if (!tbody) {
            console.error("usersTableBody element not found");
            return;
        }

        // Check permissions
        if (!this.dataManager.permissionManager.canAccess("users", "view")) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="permission-denied">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                        <h3>Ruxsat berilmagan</h3>
                        <p>Sizda foydalanuvchilar ma'lumotlarini ko'rish huquqi yo'q</p>
                    </td>
                </tr>
            `;
            return;
        }

        // Filter users by role permissions
        let users = this.dataManager.permissionManager.filterUsersByRole([
            ...this.dataManager.users,
        ]);

        // Apply search filter
        const searchQuery = this.currentSearchQueries.users;
        if (searchQuery) {
            users = this.dataManager.searchManager.searchUsers(
                users,
                searchQuery
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

        // Apply pagination
        const currentPage =
            this.dataManager.paginationManager.getCurrentPage("users");
        const paginatedUsers = this.dataManager.paginationManager.paginate(
            users,
            currentPage
        );

        // Render pagination
        this.dataManager.paginationManager.renderPagination(
            "users",
            users.length,
            () => {
                this.renderUsersTable();
            }
        );

        // Render rows
        if (paginatedUsers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="empty-state">
                        ${
                            searchQuery
                                ? "Qidiruv bo'yicha foydalanuvchilar topilmadi"
                                : "Foydalanuvchilar topilmadi"
                        }
                    </td>
                </tr>
            `;
        } else {
            const canEdit = this.dataManager.permissionManager.canAccess(
                "users",
                "edit"
            );
            const canDelete = this.dataManager.permissionManager.canAccess(
                "users",
                "delete"
            );

            tbody.innerHTML = paginatedUsers
                .map(
                    (user) => `
                <tr>
                    <td>${user.id}</td>
                    <td><strong>${user.name || "-"}</strong></td>
                    <td>${user.email || "-"}</td>
                    <td>${user.chat_id || "-"}</td>
                    <td><span class="badge ${this.getRoleBadgeClass(
                        user.role
                    )}">${this.getRoleDisplayName(user.role)}</span></td>
                    <td>${this.formatDate(user.created_at)}</td>
                    <td>
                        <div class="action-buttons">
                            ${
                                canEdit
                                    ? `
                            <button class="btn-icon" onclick="ui.editUser(${user.id})" title="Tahrirlash">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            `
                                    : ""
                            }
                            ${
                                canDelete
                                    ? `
                            <button class="btn-icon danger" onclick="ui.deleteUser(${user.id})" title="O'chirish">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3,6 5,6 21,6"/>
                                    <path d="M19,6v14a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6m3,0V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"/>
                                </svg>
                            </button>
                            `
                                    : ""
                            }
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

        this.renderUsersTable();
    }

    getRoleBadgeClass(role) {
        switch (role) {
            case "super_admin":
                return "badge-destructive";
            case "admin":
                return "badge-default";
            case "user":
            default:
                return "badge-secondary";
        }
    }

    getRoleDisplayName(role) {
        switch (role) {
            case "super_admin":
                return "Super Admin";
            case "admin":
                return "Admin";
            case "user":
            default:
                return "User";
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
            document.getElementById("talaba_id").value =
                student.talaba_id || "";
            document.getElementById("studentFish").value = student.fish || "";
            document.getElementById("studentFakultet").value =
                student.fakultet || "";
            document.getElementById("studentGuruh").value = student.guruh || "";
            document.getElementById("studentTelefon").value =
                student.telefon || "";
            document.getElementById("studentTyutori").value =
                student.tyutori || "";
            document.getElementById("studentHudud").value = student.hudud || "";
            document.getElementById("doimiy_yashash_viloyati").value =
                student.doimiy_yashash_viloyati || "";
            document.getElementById("doimiy_yashash_tumani").value =
                student.doimiy_yashash_tumani || "";
            document.getElementById("doimiy_yashash_manzili").value =
                student.doimiy_yashash_manzili || "";
            document.getElementById("doimiy_yashash_manzili_urli").value =
                student.doimiy_yashash_manzili_urli || "";
            document.getElementById("vaqtincha_yashash_viloyati").value =
                student.vaqtincha_yashash_viloyati || "";
            document.getElementById("vaqtincha_yashash_tumani").value =
                student.vaqtincha_yashash_tumani || "";
            document.getElementById("vaqtincha_yashash_manzili").value =
                student.vaqtincha_yashash_manzili || "";
            document.getElementById("vaqtincha_yashash_manzili_urli").value =
                student.vaqtincha_yashash_manzili_urli || "";
            document.getElementById("studentUyEgasi").value =
                student.uy_egasi || "";
            document.getElementById("studentUyEgasiTelefoni").value =
                student.uy_egasi_telefoni || "";
            document.getElementById("yotoqxona_nomeri").value =
                student.yotoqxona_nomeri || "";
            document.getElementById("studentNarxi").value = student.narx || "";
            document.getElementById("studentOtaOna").value =
                student.ota_ona || "";
            document.getElementById("studentOtaOnaTelefoni").value =
                student.ota_ona_telefoni || "";
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
            talaba_id: document.getElementById("talaba_id")?.value || "",
            fish: document.getElementById("studentFish")?.value || "",
            fakultet: document.getElementById("studentFakultet")?.value || "",
            guruh: document.getElementById("studentGuruh")?.value || "",
            telefon: document.getElementById("studentTelefon")?.value || "",
            tyutori: document.getElementById("studentTyutori")?.value || "",
            hudud: document.getElementById("studentHudud")?.value || "",
            doimiy_yashash_viloyati:
                document.getElementById("doimiy_yashash_viloyati")?.value || "",
            doimiy_yashash_tumani:
                document.getElementById("doimiy_yashash_tumani")?.value || "",
            doimiy_yashash_manzili:
                document.getElementById("doimiy_yashash_manzili")?.value || "",
            doimiy_yashash_manzili_urli:
                document.getElementById("doimiy_yashash_manzili_urli")?.value ||
                "",
            vaqtincha_yashash_viloyati:
                document.getElementById("vaqtincha_yashash_viloyati")?.value ||
                "",
            vaqtincha_yashash_tumani:
                document.getElementById("vaqtincha_yashash_tumani")?.value ||
                "",
            vaqtincha_yashash_manzili:
                document.getElementById("vaqtincha_yashash_manzili")?.value ||
                "",
            vaqtincha_yashash_manzili_urli:
                document.getElementById("vaqtincha_yashash_manzili_urli")
                    ?.value || "",
            uy_egasi: document.getElementById("studentUyEgasi")?.value || "",
            uy_egasi_telefoni:
                document.getElementById("studentUyEgasiTelefoni")?.value || "",
            yotoqxona_nomeri:
                document.getElementById("yotoqxona_nomeri")?.value || "",
            narx: document.getElementById("studentNarxi")?.value || "",
            ota_ona: document.getElementById("studentOtaOna")?.value || "",
            ota_ona_telefoni:
                document.getElementById("studentOtaOnaTelefoni")?.value || "",
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
        if (!this.dataManager.permissionManager.canAccess("students", "edit")) {
            alert("Sizda tahrirlash huquqi yo'q");
            return;
        }

        const student = this.dataManager.students.find((s) => s.id === id);
        if (student) {
            this.openStudentModal(student);
        }
    }

    deleteStudent(id) {
        if (
            !this.dataManager.permissionManager.canAccess("students", "delete")
        ) {
            alert("Sizda o'chirish huquqi yo'q");
            return;
        }

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
        if (!this.dataManager.permissionManager.canAccess("users", "edit")) {
            alert("Sizda tahrirlash huquqi yo'q");
            return;
        }

        const user = this.dataManager.users.find((u) => u.id === id);
        if (user) {
            this.openUserModal(user);
        }
    }

    deleteUser(id) {
        if (!this.dataManager.permissionManager.canAccess("users", "delete")) {
            alert("Sizda o'chirish huquqi yo'q");
            return;
        }

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
        currentUserRole: window.currentUserRole,
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
