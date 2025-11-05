<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Talaba Ma'lumotlari</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .modal-custom-full {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            /* Bootstrap modal z-index'idan yuqori */
            overflow-y: auto;
            padding: 1rem 0;
        }

        .modal-content-custom {
            background-color: #fff;
            margin: 1.75rem auto;
            /* Markazda bo'lishi uchun */
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            max-width: 700px;
            /* Kattaroq ekranlar uchun maksimal kenglik */
            width: 95%;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .modal-content-custom {
                margin: 0.5rem auto;
                /* Mobil ekranda yuqoriroqda */
                width: 98%;
            }
        }

        .map-placeholder {
            min-height: 300px;
            /* Xarita maydoni uchun */
            background-color: #e9ecef;
            border-radius: 0.5rem;
            border: 1px solid #dee2e6;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        /* Asl koddan olingan stilning yaxshilangan versiyasi */
        .dormity-checkbox-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* Elementlarni tarqatish */
            margin: 1rem 0;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            background-color: #f8f9fa;
            font-size: 1rem;
            color: #212529;
        }

        .dormity-checkbox-container label {
            cursor: pointer;
            flex-grow: 1;
            margin-bottom: 0;
            /* Bootstrap P tag'laridagi marginni olib tashlash */
        }

        .dormity-checkbox-container input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #0d6efd;
            /* Bootstrap primary rangiga mos keladi */
        }
    </style>
</head>

<body>
    <div class="container my-5">
        @if (session('error'))
            <div class="text-center alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @elseif(session('success'))
            <div class="text-center alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="text-end my-3">
            <form style="display: flex; flex-direction: column; align-items: flex-end" action="{{ route('verifiy.logout') }}" method="post" class="m-0 p-0 text-end">
                @csrf
                <button type="submit" class="text-danger settings-btn m-0" id="">
                    <img width="24" height="24"
                        src="https://img.icons8.com/?size=100&id=LYzWbTKzKcac&format=png&color=000000" alt="">
                    Chiqish
                </button>
            </form>
        </div>
        <div id="studentInfoCard" class="card shadow-sm border-0">
            <div class="card-body">
                <h3 class="card-title mb-4 text-primary">🎓 Talaba ma'lumotlari</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Talaba ID:</strong> {{ $student->talaba_id ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-0"><strong>F.I.Sh:</strong> {{ $student->fish ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Fakultet:</strong> {{ $student->fakultet ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Guruh:</strong> {{ $student->guruh ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Telefon:</strong> {{ $student->telefon ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Tyutori:</strong> {{ $student->tyutori ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Hudud:</strong> {{ $student->hudud ?? '—' }}</p>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="text-secondary mb-3">Doimiy Yashash Manzili</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Viloyat:</strong> {{ $student->doimiy_yashash_viloyati ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Tuman:</strong> {{ $student->doimiy_yashash_tumani ?? '—' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-0"><strong>Manzil:</strong> {{ $student->doimiy_yashash_manzili ?? '—' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-0"><strong>Manzil URL:</strong> <a
                                href="{{ $student->doimiy_yashash_manzili_urli ?? '#' }}"
                                target="_blank">{{ $student->doimiy_yashash_manzili_urli ? "Xaritadan ko'rish" : '—' }}</a>
                        </p>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="text-secondary mb-3">Vaqtincha Yashash Joyi</h5>
                @php
                    $isDormity = $student->dormity == 1; // Agar 'dormity' maydoni bo'lsa va u '1' ga teng bo'lsa
                @endphp
                <div class="row g-3">
                    <div class="col-12">
                        <p class="mb-0"><strong>Joy turi:</strong>
                            {{ $student->yotoqxona_nomeri ? 'Yotoqxona' : 'Ijara' }}</p>
                    </div>

                    @if ($student->yotoqxona_nomeri)
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Yotoqxona Nomeri:</strong>
                                {{ $student->yotoqxona_nomeri ?? '—' }}</p>
                        </div>
                    @else
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Viloyat:</strong>
                                {{ $student->vaqtincha_yashash_viloyati ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Tuman:</strong> {{ $student->vaqtincha_yashash_tumani ?? '—' }}
                            </p>
                        </div>
                        <div class="col-12">
                            <p class="mb-0"><strong>Manzil:</strong> {{ $student->vaqtincha_yashash_manzili ?? '—' }}
                            </p>
                        </div>
                        <div class="col-12">
                            <p class="mb-0"><strong>Manzil URL:</strong> <a
                                    href="{{ $student->vaqtincha_yashash_manzili_urli ?? '#' }}"
                                    target="_blank">{{ $student->vaqtincha_yashash_manzili_urli ? "Xaritadan ko'rish" : '—' }}</a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Uy Egasi:</strong> {{ $student->uy_egasi ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Uy Egasi Tel:</strong> {{ $student->uy_egasi_telefoni ?? '—' }}
                            </p>
                        </div>
                    @endif
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Narxi:</strong>
                            {{ $student->narx ? number_format($student->narx, 0, '', ' ') . ' so\'m' : '—' }}</p>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="text-secondary mb-3">Qo'shimcha Ma'lumotlar</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Ota - Ona:</strong> {{ $student->ota_ona ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-0"><strong>Ota - Ona Tel:</strong> {{ $student->ota_ona_telefoni ?? '—' }}</p>
                    </div>
                </div>

                <button id="editStudentBtn" class="btn btn-primary mt-4 w-100">✏️ Tahrirlash</button>
                <form action="{{ route('verifiy.newPassword', $student->id) }}" method="post" id="editpassform">
                    @csrf
                    <h5 class="text-secondary mb-3">Yangi parol</h5>
                    <div class="row g-3 mb-4">
                        @if (isset($student->verifiy->password))
                            <div class="col-md-6">
                                <label for="nowpassword" class="form-label">Joriy parol</label>
                                <input name="nowpassword" type="password" id="nowpassword" class="form-control"
                                    required>
                                @error('nowpassword')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @if (session('error'))
                                    <div class="text-danger">{{ session('error') }}</div>
                                @endif
                            </div>
                        @endif
                        <div class="col-12"></div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Yangi parol</label>
                            <input name="password" type="password" id="password" class="form-control" required>
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirm" class="form-label">Yangi parol qaytaring</label>
                            <input name="password_confirmation" type="password" id="password_confirm"
                                class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-4 w-100">✏️ Parolni saqlash</button>
                </form>
            </div>
        </div>

        <div class="modal-custom-full" id="studentModal">
            <div class="modal-content-custom">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                    <h4 id="studentModalTitle" class="mb-0 text-primary">Talaba Ma'lumotlarini Yangilash</h4>
                    <button type="button" class="btn-close" aria-label="Close" id="closeStudentModal"></button>
                </div>

                <form action="{{ route('verifiy.update', $student->id) }}" method="post" id="studentForm">
                    @csrf
                    <h5 class="text-secondary mb-3">Asosiy Ma'lumotlar</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="talaba_id" class="form-label">Talaba ID *</label>
                            <input readonly name="talaba_id" value="{{ $student->talaba_id ?? '' }}" type="number"
                                id="talaba_id" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="studentFish" class="form-label">F.I.Sh *</label>
                            <input name="fish" value="{{ $student->fish ?? '' }}" type="text"
                                id="studentFish" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="studentFakultet" class="form-label">Fakultet *</label>
                            <input name="fakultet" value="{{ $student->fakultet ?? '' }}" type="text"
                                id="studentFakultet" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="studentGuruh" class="form-label">Guruh *</label>
                            <input name="guruh" value="{{ $student->guruh ?? '' }}" type="text"
                                id="studentGuruh" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="studentTelefon" class="form-label">Telefon</label>
                            <input name="telefon" value="{{ $student->telefon ?? '' }}" type="text"
                                id="studentTelefon" class="form-control" placeholder="+998901234567">
                        </div>
                        <div class="col-md-6">
                            <label for="studentTyutori" class="form-label">Tyutori</label>
                            <input name="tyutori" value="{{ $student->tyutori ?? '' }}" type="text"
                                id="studentTyutori" class="form-control">
                        </div>
                        <div class="col-12">
                            <label for="studentHudud" class="form-label">Hudud</label>
                            <input name="hudud" value="{{ $student->hudud ?? '' }}" type="text"
                                id="studentHudud" class="form-control">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="text-secondary mb-3">📍 Doimiy Yashash Manzili</h5>
                    <div id="mapPermanent" class="map-placeholder mb-3">
                        <small>Doimiy yashash joyi xaritasi</small>
                    </div>
                    <small class="text-muted d-block mb-3">Xaritadan tanlasangiz quyidagi maydonlar avtomatik
                        to'ldiriladi</small>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="doimiy_yashash_viloyati" class="form-label">Viloyat</label>
                            <select id="doimiy_yashash_viloyati" name="doimiy_yashash_viloyati" class="form-select">
                                <option value="" disabled selected>Viloyatni tanlang...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="doimiy_yashash_tumani" class="form-label">Tuman</label>
                            <select id="doimiy_yashash_tumani" name="doimiy_yashash_tumani" class="form-select">
                                <option value="" disabled selected>Tumanni tanlang...</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="doimiy_yashash_manzili" class="form-label">Manzil</label>
                            <input type="text" name="doimiy_yashash_manzili"
                                value="{{ $student->doimiy_yashash_manzili ?? '' }}" id="doimiy_yashash_manzili"
                                class="form-control" placeholder="To'liq manzil">
                        </div>
                        <div class="col-12">
                            <label for="doimiy_yashash_manzili_urli" class="form-label">URL</label>
                            <input type="url" name="doimiy_yashash_manzili_urli"
                                value="{{ $student->doimiy_yashash_manzili_urli ?? '' }}"
                                id="doimiy_yashash_manzili_urli" class="form-control"
                                placeholder="Manzil URL avtomatik yoziladi">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="text-secondary mb-3">📍 Vaqtincha Yashash Joyi</h5>

                    <div class="dormity-checkbox-container">
                        <label for="dormity">Yotoqxona</label>
                        <input type="checkbox" name="dormity" id="dormity" class="form-check-input"
                            value="1" {{ isset($student->dormity) && $student->dormity == 1 ? 'checked' : '' }}>
                    </div>

                    <div id="ijara"
                        style="display: {{ isset($student->dormity) && $student->dormity == 1 ? 'none' : 'block' }};">
                        <div id="mapTemporary" class="map-placeholder mb-3">
                            <small>Vaqtincha yashash joyi xaritasi</small>
                        </div>
                        <small class="text-muted d-block mb-3">Xaritadan tanlasangiz quyidagi maydonlar avtomatik
                            to'ldiriladi</small>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="vaqtincha_yashash_viloyati" class="form-label">Viloyat</label>
                                <select id="vaqtincha_yashash_viloyati" name="vaqtincha_yashash_viloyati"
                                    class="form-select">
                                    <option value="" disabled selected>Viloyatni tanlang...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="vaqtincha_yashash_tumani" class="form-label">Tuman</label>
                                <select id="vaqtincha_yashash_tumani" name="vaqtincha_yashash_tumani"
                                    class="form-select">
                                    <option value="" disabled selected>Tumanni tanlang...</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="vaqtincha_yashash_manzili" class="form-label">Manzil</label>
                                <input type="text" name="vaqtincha_yashash_manzili"
                                    value="{{ $student->vaqtincha_yashash_manzili ?? '' }}"
                                    id="vaqtincha_yashash_manzili" class="form-control" placeholder="To'liq manzil">
                            </div>
                            <div class="col-12">
                                <label for="vaqtincha_yashash_manzili_urli" class="form-label">URL</label>
                                <input type="url" name="vaqtincha_yashash_manzili_urli"
                                    value="{{ $student->vaqtincha_yashash_manzili_urli ?? '' }}"
                                    id="vaqtincha_yashash_manzili_urli" class="form-control"
                                    placeholder="Manzil URL avtomatik yoziladi">
                            </div>
                            <div class="col-md-6">
                                <label for="studentUyEgasi" class="form-label">Uy egasi</label>
                                <input name="uy_egasi" value="{{ $student->uy_egasi ?? '' }}" type="text"
                                    id="studentUyEgasi" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="studentUyEgasiTelefoni" class="form-label">Uy egasi telefoni</label>
                                <input name="uy_egasi_telefoni" value="{{ $student->uy_egasi_telefoni ?? '' }}"
                                    type="text" id="studentUyEgasiTelefoni" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div id="yotoqxona" class="mb-4" style="display: none;">
                        <label for="yotoqxona_nomeri" class="form-label">Yotoqxona raqami</label>
                        <select id="yotoqxona_nomeri" name="yotoqxona_nomeri" class="form-select">
                            <option value="" disabled selected>Yotoqxona nomerini tanlang...</option>
                            <option value="1-sonli"
                                {{ isset($student->yotoqxona_nomeri) && $student->yotoqxona_nomeri == '1-sonli' ? 'selected' : '' }}>
                                1-sonli</option>
                            <option value="2-sonli"
                                {{ isset($student->yotoqxona_nomeri) && $student->yotoqxona_nomeri == '2-sonli' ? 'selected' : '' }}>
                                2-sonli</option>
                            <option value="3-sonli"
                                {{ isset($student->yotoqxona_nomeri) && $student->yotoqxona_nomeri == '3-sonli' ? 'selected' : '' }}>
                                3-sonli</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="studentNarxi" class="form-label">Narxi</label>
                            <input name="narx" value="{{ $student->narx ?? '' }}" type="number"
                                id="studentNarxi" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="studentOtaOna" class="form-label">Ota - Ona</label>
                            <input name="ota_ona" value="{{ $student->ota_ona ?? '' }}" type="text"
                                id="studentOtaOna" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="studentOtaOnaTelefoni" class="form-label">Ota - Ona telefoni</label>
                            <input name="ota_ona_telefoni" value="{{ $student->ota_ona_telefoni ?? '' }}"
                                type="text" id="studentOtaOnaTelefoni" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="button" class="btn btn-secondary" id="cancelStudentBtn">Bekor qilish</button>
                        <button type="submit" class="btn btn-primary" id="saveStudentBtn">Saqlash</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const studentCard = document.getElementById('studentInfoCard');
        const studentModal = document.getElementById('studentModal');
        const editBtn = document.getElementById('editStudentBtn');
        const cancelBtn = document.getElementById('cancelStudentBtn');
        const closeBtn = document.getElementById('closeStudentModal');
        const dormityCheckbox = document.getElementById('dormity');
        const ijaraDiv = document.getElementById('ijara');
        const yotoqxonaDiv = document.getElementById('yotoqxona');

        // Modalni ochish
        editBtn.addEventListener('click', () => {
            studentCard.style.display = 'none';
            studentModal.style.display = 'block';
        });

        // Modalni yopish
        cancelBtn.addEventListener('click', () => {
            studentModal.style.display = 'none';
            studentCard.style.display = 'block';
        });

        closeBtn.addEventListener('click', () => {
            studentModal.style.display = 'none';
            studentCard.style.display = 'block';
        });

        // Yotoqxona/Ijara o'zgarishini boshqarish
        function toggleAccommodation() {
            if (dormityCheckbox.checked) {
                ijaraDiv.style.display = 'none';
                yotoqxonaDiv.style.display = 'block';
            } else {
                ijaraDiv.style.display = 'block';
                yotoqxonaDiv.style.display = 'none';
            }
        }

        // Dastlabki holatni o'rnatish
        document.addEventListener('DOMContentLoaded', toggleAccommodation);

        // Checkbox o'zgarishini kuzatish
        dormityCheckbox.addEventListener('change', toggleAccommodation);
    </script>


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


</body>

</html>
