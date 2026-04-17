// ============================================
// SIPJUR - Client-Side JavaScript
// Validasi Form & Interaktivitas
// ============================================

document.addEventListener('DOMContentLoaded', function () {

    // =====================
    // FORM VALIDATION
    // =====================

    // Validasi form login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            const nimNip = document.getElementById('nim_nip').value.trim();
            const password = document.getElementById('password').value;
            let errors = [];

            if (!nimNip) errors.push('NIM/NIP wajib diisi');
            if (!password) errors.push('Password wajib diisi');

            if (errors.length > 0) {
                e.preventDefault();
                showAlert(errors.join('<br>'), 'danger');
            }
        });
    }

    // Validasi form register
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            const nim = document.getElementById('nim_nip').value.trim();
            const nama = document.getElementById('nama').value.trim();
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            let errors = [];

            if (!nim) errors.push('NIM wajib diisi');
            if (nim && !/^\d+$/.test(nim)) errors.push('NIM harus berupa angka');
            if (!nama) errors.push('Nama wajib diisi');
            if (!password) errors.push('Password wajib diisi');
            if (password.length < 6) errors.push('Password minimal 6 karakter');
            if (password !== confirm) errors.push('Konfirmasi password tidak cocok');

            if (errors.length > 0) {
                e.preventDefault();
                showAlert(errors.join('<br>'), 'danger');
            }
        });
    }

    // Validasi form pengajuan surat
    const ajukanForm = document.getElementById('ajukanForm');
    if (ajukanForm) {
        ajukanForm.addEventListener('submit', function (e) {
            const jenisSurat = document.getElementById('jenis_surat_id').value;
            let errors = [];

            if (!jenisSurat) errors.push('Jenis surat wajib dipilih');

            // Validasi file
            const fileInput = document.getElementById('file_lampiran');
            if (fileInput && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const allowedExts = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
                const ext = file.name.split('.').pop().toLowerCase();

                if (!allowedExts.includes(ext)) {
                    errors.push('Format file tidak didukung');
                }
                if (file.size > 5 * 1024 * 1024) {
                    errors.push('Ukuran file maksimal 5MB');
                }
            }

            if (errors.length > 0) {
                e.preventDefault();
                showAlert(errors.join('<br>'), 'danger');
            }
        });
    }

    // =====================
    // MODAL FUNCTIONS
    // =====================

    // Buka modal
    window.openModal = function (modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    };

    // Tutup modal
    window.closeModal = function (modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }
    };

    // Tutup modal saat klik overlay
    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                overlay.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        });
    });

    // =====================
    // SET PENGAJUAN ID UNTUK MODAL ACTION
    // =====================
    window.setActionId = function (modalId, pengajuanId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const idInput = modal.querySelector('input[name="id"]');
            if (idInput) idInput.value = pengajuanId;
            openModal(modalId);
        }
    };

    // =====================
    // SIDEBAR TOGGLE (MOBILE)
    // =====================
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }

    // =====================
    // AUTO HIDE ALERTS
    // =====================
    const alerts = document.querySelectorAll('.alert-auto-hide');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // =====================
    // SEARCH/FILTER TABEL
    // =====================
    const searchInput = document.getElementById('searchTable');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase();
            const rows = document.querySelectorAll('#dataTable tbody tr');
            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    }
});

// =====================
// UTILITY FUNCTIONS
// =====================
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-auto-hide';
    alertDiv.innerHTML = message;

    const container = document.querySelector('.alert-container') || document.querySelector('.content-area') || document.querySelector('.login-card');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        setTimeout(() => {
            alertDiv.style.transition = 'opacity 0.5s';
            alertDiv.style.opacity = '0';
            setTimeout(() => alertDiv.remove(), 500);
        }, 4000);
    }
}

// Konfirmasi sebelum aksi
function confirmAction(message) {
    return confirm(message || 'Apakah Anda yakin?');
}
