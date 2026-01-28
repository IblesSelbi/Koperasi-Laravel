@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="fw-semibold mb-1">Profil Saya</h4>
            <p class="text-muted fs-3 mb-0">Kelola informasi profil Anda</p>
        </div>
    </div>

    {{-- Profile Image Section --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-4">
                        {{-- Profile Image Upload --}}
                        <form action="{{ route('user.profile.updateImage') }}" method="POST" enctype="multipart/form-data"
                            id="formProfileImage">
                            @csrf
                            @method('PATCH')

                            <label for="profile_image" class="cursor-pointer position-relative">
                                <img id="previewImage"
                                    src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/images/profile/user-1.jpg') }}"
                                    alt="Profile Image"
                                    class="rounded-circle object-fit-cover border border-2 border-light-subtle shadow-sm hover-opacity-75 transition"
                                    style="width: 120px; height: 120px; cursor: pointer;" />

                                {{-- Edit Icon --}}
                                <span class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow-sm p-2"
                                    style="cursor: pointer;">
                                    <i class="ti ti-camera text-primary" style="font-size: 20px;"></i>
                                </span>
                            </label>

                            <input type="file" id="profile_image" name="profile_image" accept="image/*" class="d-none">
                        </form>

                        {{-- Profile Info --}}
                        <div class="flex-grow-1">
                            <h5 class="fw-semibold mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-2">
                                <i class="ti ti-mail me-1"></i>{{ $user->email }}
                            </p>
                            <span class="badge bg-primary-subtle text-primary rounded-pill shadow-sm">
                                <i class="ti ti-user me-1"></i>{{ ucfirst($user->role->nama ?? 'User') }}
                            </span>
                        </div>

                        {{-- Quick Stats - Professional Design (Bootstrap 5 Fixed) --}}
                        <div class="d-none d-lg-flex align-items-center gap-4">
                            {{-- Role Badge --}}
                            <div class="text-center px-3">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="rounded-circle bg-primary-subtle d-inline-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px; min-width: 50px; min-height: 50px;">
                                        <i class="ti ti-user text-primary fs-4"></i>
                                    </div>
                                </div>
                                <h6 class="fw-semibold mb-0 text-dark">{{ ucfirst($user->role->nama ?? 'User') }}</h6>
                                <small class="text-muted">Role</small>
                            </div>

                            <div class="vr" style="height: 60px;"></div>

                            {{-- Verification Status --}}
                            <div class="text-center px-3">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    @if($user->email_verified_at)
                                        <div class="rounded-circle bg-success-subtle d-inline-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px; min-width: 50px; min-height: 50px;">
                                            <i class="ti ti-check text-success fs-4"></i>
                                        </div>
                                    @else
                                        <div class="rounded-circle bg-danger-subtle d-inline-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px; min-width: 50px; min-height: 50px;">
                                            <i class="ti ti-x text-danger fs-4"></i>
                                        </div>
                                    @endif
                                </div>
                                <h6 class="fw-semibold mb-0 text-dark">
                                    {{ $user->email_verified_at ? 'Verified' : 'Not Verified' }}
                                </h6>
                                <small class="text-muted">Status (Gmail)</small>
                            </div>

                            {{-- Member Since --}}
                            <div class="vr" style="height: 60px;"></div>

                            <div class="text-center px-3">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <div class="rounded-circle bg-info-subtle d-inline-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px; min-width: 50px; min-height: 50px;">
                                        <i class="ti ti-calendar text-info fs-4"></i>
                                    </div>
                                </div>
                                <h6 class="fw-semibold mb-0 text-dark">
                                    {{ $user->created_at ? $user->created_at->TranslatedFormat('d M Y') : '-' }}
                                </h6>
                                <small class="text-muted">Member Since</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    @include('user.profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    @include('user.profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .hover-opacity-75:hover {
            opacity: 0.75;
            transition: opacity 0.3s ease;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .transition {
            transition: all 0.3s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #previewImage {
                width: 80px !important;
                height: 80px !important;
            }

            .position-absolute.bottom-0.end-0 i {
                font-size: 16px !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Preview and auto-submit profile image
        document.getElementById('profile_image').addEventListener('change', function (event) {
            const input = event.target;
            const preview = document.getElementById('previewImage');

            if (input.files && input.files[0]) {
                // Validate file size (max 2MB)
                if (input.files[0].size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 2MB');
                    input.value = '';
                    return;
                }

                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!validTypes.includes(input.files[0].type)) {
                    alert('Format file tidak valid! Gunakan JPG, PNG, atau GIF');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    // Auto submit form after preview
                    document.getElementById('formProfileImage').submit();
                };
                reader.readAsDataURL(input.files[0]);
            }
        });

        // Show loading state when uploading
        document.getElementById('formProfileImage').addEventListener('submit', function () {
            const preview = document.getElementById('previewImage');
            preview.style.opacity = '0.5';

            // Optional: Add loading spinner
            const spinner = document.createElement('div');
            spinner.className = 'position-absolute top-50 start-50 translate-middle';
            spinner.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
            preview.parentElement.appendChild(spinner);
        });
    </script>
@endpush