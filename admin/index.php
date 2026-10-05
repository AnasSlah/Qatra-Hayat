<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قطرة حياة - الهيكل الأساسي</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* المتغيرات (الألوان الأساسية) */
        :root {
            --primary-red: #df3e4d;
            --bg-color: #f4f6f9;
            --white: #ffffff;
            --text-dark: #333333;
            --text-gray: #777777;
            --border-color: #eaeaea;
        }

        /* إعدادات عامة */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            display: flex;
            min-height: 100vh;
        }

        /* القائمة الجانبية (Sidebar) */
        .sidebar {
            width: 260px;
            background-color: var(--white);
            border-left: 1px solid var(--border-color);
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-dark);
            text-align: center;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .logo i {
            color: var(--primary-red);
        }

        .nav-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 0 15px;
            flex-grow: 1; /* للسماح للقائمة بالتمدد ودفع زر الخروج للأسفل */
        }

        .nav-links li a {
            text-decoration: none;
            color: var(--text-gray);
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
        }

        .nav-links li a:hover {
            background-color: #f9f9f9;
        }

        .nav-links li.active a {
            background-color: var(--primary-red);
            color: var(--white);
        }

        /* تنسيق خاص لزر تسجيل الخروج */
        .logout-item {
            margin-top: auto; /* يدفعه لأسفل القائمة */
            border-top: 1px solid var(--border-color);
            padding-top: 25px; /* زيادة المسافة العلوية */
            margin-bottom: 20px; /* مسافة سفلية */
        }

        .logout-item a {
            color: var(--primary-red) !important;
            display: flex;
            justify-content: center; /* توسيط النص والأيقونة معاً */
            align-items: center;
            gap: 12px;
            font-weight: 700; /* جعل الخط أعرض قليلاً ليبرز الزر */
            background-color: transparent;
        }

        .logout-item a:hover {
            background-color: #fde8e8 !important;
        }

        /* المحتوى الرئيسي (Main Content) */
        .main-content {
            flex: 1;
            padding: 20px 40px;
            display: flex;
            flex-direction: column;
            gap: 25px;
            overflow-y: auto;
        }

        /* الشريط العلوي (Header) */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--white);
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: var(--primary-red);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .notification {
            position: relative;
            cursor: pointer;
            color: var(--text-gray);
            font-size: 20px;
        }

        .notification .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--primary-red);
            color: var(--white);
            font-size: 10px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* مساحة العمل (مكان الملفات التي ستضاف لاحقاً) */
        .content-area {
            flex: 1;
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-droplet"></i> قطرة حياة
        </div>
        <ul class="nav-links">
            <li class="active"><a href="home.php"><i class="fa-solid fa-house"></i> الرئيسية</a></li>
            <li><a href="records.php"><i class="fa-regular fa-clipboard"></i> السجل</a></li>
            <li><a href="accounts.php"><i class="fa-solid fa-users"></i> المتبرعين</a></li>
            <li><a href="centers.php"><i class="fa-solid fa-location-dot"></i> المراكز</a></li>
            <li><a href="my-account.php"><i class="fa-regular fa-user"></i> حسابي</a></li>
            <li><a href="contact.php"><i class="fa-solid fa-phone"></i> تواصل</a></li>
            
            <!-- زر تسجيل الخروج -->
            <li class="logout-item">
                <a href="logout.php">تسجيل الخروج <i class="fa-solid fa-right-from-bracket"></i></a>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        
        <header class="header">
            <div class="user-info">
                <div class="user-avatar">أ</div>
                <span style="font-weight: 600;">مرحباً، أحمد 👋</span>
            </div>
            <div class="notification">
                <i class="fa-regular fa-bell"></i>
                <span class="badge">1</span>
            </div>
        </header>

        <div class="content-area"></div>

    </main>

    <div id="reject-modal-container"></div>
    <div id="donors-modal-container"></div>
    <div id="edit-modal-container"></div>
    <div id="profile-modal-container"></div>
    <div id="delete-account-modal-container"></div>
    <div id="delete-center-modal-container"></div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const contentArea = document.querySelector('.content-area');
        const modalContainers = {
            reject: document.getElementById('reject-modal-container'),
            donors: document.getElementById('donors-modal-container'),
            edit: document.getElementById('edit-modal-container'),
            profile: document.getElementById('profile-modal-container'),
            deleteAcc: document.getElementById('delete-account-modal-container'),
            deleteCenter: document.getElementById('delete-center-modal-container')
        };

        // تحميل ملفات البوب أب بصيغة PHP
        fetch('reject-modal.php').then(r => r.text()).then(h => modalContainers.reject.innerHTML = h).catch(e => console.log('not found'));
        fetch('donors-modal.php').then(r => r.text()).then(h => modalContainers.donors.innerHTML = h).catch(e => console.log('not found'));
        fetch('edit-account-modal.php').then(r => r.text()).then(h => modalContainers.edit.innerHTML = h).catch(e => console.log('not found'));
        fetch('profile-modal.php').then(r => r.text()).then(h => modalContainers.profile.innerHTML = h).catch(e => console.log('not found'));
        fetch('delete-account-modal.php').then(r => r.text()).then(h => modalContainers.deleteAcc.innerHTML = h).catch(e => console.log('not found'));
        fetch('delete-center-modal.php').then(r => r.text()).then(h => modalContainers.deleteCenter.innerHTML = h).catch(e => console.log('not found'));

        function loadPage(url) {
            fetch(url)
                .then(r => r.text())
                .then(h => contentArea.innerHTML = h)
                .catch(e => contentArea.innerHTML = `<p>حدث خطأ في التحميل.</p>`);
        }

        // تحميل صفحة الهوم
        loadPage('home.php');

        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', function (e) {
                const targetPage = this.getAttribute('href');
                
                // التعديل هنا: استثناء logout.php من الفتح داخل مساحة العمل ليتم الانتقال فعلياً
                if (targetPage !== '#' && targetPage.endsWith('.php') && targetPage !== 'logout.php') {
                    e.preventDefault(); 
                    loadPage(targetPage); 
                    
                    // إزالة الكلاس من جميع الروابط ما عدا تسجيل الخروج
                    document.querySelectorAll('.nav-links li').forEach(li => li.classList.remove('active'));
                    this.parentElement.classList.add('active');
                }
            });
        });

        document.body.addEventListener('click', function(e) {
            
            /* ================= [ 1. تعديل بيانات حساب الأدمن ] ================= */
            const startEditBtn = e.target.closest('#startEditProfileBtn');
            if (startEditBtn) {
                const panel = startEditBtn.closest('.settings-panel');
                panel.classList.add('is-editing');
                startEditBtn.style.display = 'none';
                panel.querySelector('#saveProfileBtn').style.display = 'flex';
                panel.querySelector('#cancelEditProfileBtn').style.display = 'flex';
                return;
            }

            const cancelEditBtn = e.target.closest('#cancelEditProfileBtn');
            if (cancelEditBtn) {
                const panel = cancelEditBtn.closest('.settings-panel');
                panel.classList.remove('is-editing');
                panel.querySelector('#startEditProfileBtn').style.display = 'flex';
                panel.querySelector('#saveProfileBtn').style.display = 'none';
                cancelEditBtn.style.display = 'none';
                panel.querySelectorAll('.field-container').forEach(container => {
                    const span = container.querySelector('.view-mode');
                    const input = container.querySelector('.edit-mode');
                    if (input && span && input.type !== 'password') {
                        input.value = span.innerText;
                    } else if(input && input.type === 'password') {
                        input.value = ""; 
                    }
                });
                return;
            }

            const saveProfileBtn = e.target.closest('#saveProfileBtn');
            if (saveProfileBtn) {
                const nameVal = document.getElementById('adminNameInput').value.trim();
                const emailVal = document.getElementById('adminEmailInput').value.trim();
                const phoneVal = document.getElementById('adminPhoneInput').value.trim();
                
                const newPassVal = document.getElementById('newPasswordInput') ? document.getElementById('newPasswordInput').value : '';
                const confirmPassVal = document.getElementById('confirmPasswordInput') ? document.getElementById('confirmPasswordInput').value : '';

                if (newPassVal !== "" && newPassVal !== confirmPassVal) {
                    alert("كلمة المرور الجديدة غير متطابقة مع التأكيد!");
                    return;
                }

                const originalText = saveProfileBtn.innerHTML;
                saveProfileBtn.innerHTML = "جاري الحفظ...";
                saveProfileBtn.disabled = true;

                const formData = new FormData();
                formData.append('action', 'edit_profile');
                formData.append('name', nameVal);
                formData.append('email', emailVal);
                formData.append('phone', phoneVal);
                formData.append('new_password', newPassVal);

                fetch('my-account.php', { method: 'POST', body: formData })
                .then(res => res.text())
                .then(data => {
                    alert(data); 
                    document.querySelector('.nav-links a[href="my-account.php"]').click();
                })
                .catch(err => {
                    alert("حدث خطأ في الاتصال بالسيرفر!");
                    saveProfileBtn.innerHTML = originalText;
                    saveProfileBtn.disabled = false;
                });
                return;
            }

            /* ================= [ 2. عرض الملف الشامل للمتبرع ] ================= */
            const viewBtn = e.target.closest('.btn-view');
            if (viewBtn) {
                const tableRow = viewBtn.closest('tr');
                if (tableRow) {
                    const name = tableRow.querySelector('.user-cell-info h4').innerText;
                    const membershipId = tableRow.querySelector('.user-cell-info p').innerText;
                    const bloodTypeCell = tableRow.querySelector('.blood-type-text, .blood-badge-sm');
                    const bloodType = bloodTypeCell ? bloodTypeCell.innerText.trim() : "";
                    const phone = tableRow.cells[2].innerText.trim();
                    const address = tableRow.cells[3].innerText;
                    const profileModal = document.getElementById('profileModal');
                    if (profileModal) {
                        document.getElementById('viewFullName').value = name;
                        document.getElementById('viewPhone').value = phone;
                        document.getElementById('viewAddress').value = address;
                        document.getElementById('viewCardName').innerText = name;
                        document.getElementById('viewCardId').innerText = membershipId;
                        document.getElementById('viewCardBlood').innerText = "فصيلة الدم: " + bloodType;
                        profileModal.classList.add('active');
                    }
                }
                return;
            }
            const closeProfileBtn = e.target.closest('#closeProfileModalBtn') || e.target.closest('#profileModal');
            if (closeProfileBtn && e.target.id !== 'profileModalBox') {
                if (e.target.id === 'profileModal' || e.target.closest('#closeProfileModalBtn')) {
                    const profileModal = document.getElementById('profileModal');
                    if (profileModal) profileModal.classList.remove('active');
                    return;
                }
            }

            /* ================= [ 3. تعديل حساب المتبرع ] ================= */
            const editAccBtn = e.target.closest('.btn-edit');
            if (editAccBtn && !editAccBtn.classList.contains('center-edit-btn') && !editAccBtn.classList.contains('req-edit-btn')) {
                const tableRow = editAccBtn.closest('tr');
                if (tableRow) {
                    const userId = editAccBtn.getAttribute('data-id');
                    let name = "";
                    const nameH4 = tableRow.querySelector('.user-cell-info h4');
                    if (nameH4) name = nameH4.innerText; 
                    else if (tableRow.querySelector('.donor-info h4')) name = tableRow.querySelector('.donor-info h4').innerText; 
                    else name = tableRow.cells[0].innerText.trim(); 

                    const bloodTypeCell = tableRow.querySelector('.blood-type-text, .blood-badge-sm');
                    const bloodType = bloodTypeCell ? bloodTypeCell.innerText.trim() : "";
                    
                    const editModal = document.getElementById('editAccountModal');
                    if (editModal) {
                        document.getElementById('editUserIdInput').value = userId;
                        if(document.getElementById('editMembershipId')) document.getElementById('editMembershipId').value = ""; 
                        if(document.getElementById('editNationalId')) document.getElementById('editNationalId').value = ""; 
                        document.getElementById('editFullName').value = name;
                        
                        const bloodSelect = document.getElementById('editBloodType');
                        if(bloodSelect && bloodType !== "") bloodSelect.value = bloodType;
                        
                        document.getElementById('editPhone').value = "";
                        document.getElementById('editAddress').value = "";
                        if(document.getElementById('editEmail')) document.getElementById('editEmail').value = "";
                        if(document.getElementById('editBirthDate')) document.getElementById('editBirthDate').value = "";
                        if(document.getElementById('editChronicDiseases')) document.getElementById('editChronicDiseases').value = "";
                        
                        editModal.classList.add('active');
                    }
                }
                return;
            }
            
            const closeEditAccBtn = e.target.closest('#closeEditModalBtn') || e.target.closest('#cancelEditBtn');
            if (closeEditAccBtn || e.target.id === 'editAccountModal') {
                const editModal = document.getElementById('editAccountModal');
                if(editModal) editModal.classList.remove('active');
                return;
            }

            const confirmSaveEditBtn = e.target.closest('#confirmSaveEditBtn');
            if (confirmSaveEditBtn) {
                const userId         = document.getElementById('editUserIdInput').value; 
                const fullName       = document.getElementById('editFullName').value;
                const nationalId     = document.getElementById('editNationalId') ? document.getElementById('editNationalId').value : '';
                const birthDate      = document.getElementById('editBirthDate') ? document.getElementById('editBirthDate').value : '';
                const bloodType      = document.getElementById('editBloodType').value;
                const phone          = document.getElementById('editPhone').value;
                const email          = document.getElementById('editEmail') ? document.getElementById('editEmail').value : '';
                const address        = document.getElementById('editAddress').value;
                const chronicDiseases= document.getElementById('editChronicDiseases') ? document.getElementById('editChronicDiseases').value : '';
                const accountStatus  = document.getElementById('editAccountStatus') ? document.getElementById('editAccountStatus').value : '1';

                const originalText = confirmSaveEditBtn.innerHTML;
                confirmSaveEditBtn.innerHTML = "جاري الحفظ...";
                confirmSaveEditBtn.disabled = true;

                const formData = new FormData();
                formData.append('user_id', userId); 
                formData.append('full_name', fullName);
                formData.append('national_id', nationalId);
                formData.append('birth_date', birthDate);
                formData.append('blood_type', bloodType);
                formData.append('phone', phone);
                formData.append('email', email);
                formData.append('address', address);
                formData.append('chronic_diseases', chronicDiseases);
                formData.append('account_status', accountStatus);

                fetch('edit-account-modal.php', { method: 'POST', body: formData })
                .then(response => response.text())
                .then(data => {
                    alert(data); 
                    const editModal = document.getElementById('editAccountModal');
                    if(editModal) editModal.classList.remove('active');
                    confirmSaveEditBtn.innerHTML = originalText;
                    confirmSaveEditBtn.disabled = false;
                    const activeLink = document.querySelector('.nav-links li.active a');
                    if (activeLink) activeLink.click();
                })
                .catch(error => {
                    alert("حدث خطأ في الاتصال بالسيرفر!");
                    confirmSaveEditBtn.innerHTML = originalText;
                    confirmSaveEditBtn.disabled = false;
                });
                return;
            }

            /* ================= [ 4. حذف حساب المتبرع ] ================= */
            const deleteAccBtn = e.target.closest('.btn-delete');
            if (deleteAccBtn && !deleteAccBtn.classList.contains('center-delete-btn') && !deleteAccBtn.classList.contains('req-delete-btn')) {
                const tableRow = deleteAccBtn.closest('tr');
                if (tableRow) {
                    const userId = deleteAccBtn.getAttribute('data-id') || 0; 
                    const nameElement = tableRow.querySelector('.user-cell-info h4') || tableRow.cells[0];
                    const name = nameElement.innerText.trim();
                    
                    const deleteModal = document.getElementById('deleteAccountModal');
                    if (deleteModal) {
                        document.getElementById('deleteAccountNameDisplay').innerText = name;
                        if(document.getElementById('deleteAccountIdInput')) {
                            document.getElementById('deleteAccountIdInput').value = userId;
                        }
                        deleteModal.classList.add('active');
                    }
                }
                return;
            }
            const cancelDeleteAccBtn = e.target.closest('#cancelDeleteAccountBtn');
            if (cancelDeleteAccBtn || e.target.id === 'deleteAccountModal') {
                const deleteModal = document.getElementById('deleteAccountModal');
                if (deleteModal) deleteModal.classList.remove('active');
                return;
            }
            const confirmDeleteAccBtn = e.target.closest('#confirmDeleteAccountBtn');
            if (confirmDeleteAccBtn) {
                const userId = document.getElementById('deleteAccountIdInput') ? document.getElementById('deleteAccountIdInput').value : 0;
                const originalText = confirmDeleteAccBtn.innerHTML;
                confirmDeleteAccBtn.innerHTML = "جاري الحذف...";
                confirmDeleteAccBtn.disabled = true;

                const formData = new FormData();
                formData.append('user_id', userId);

                fetch('delete-account-modal.php', { method: 'POST', body: formData })
                .then(response => response.text())
                .then(data => {
                    alert(data); 
                    const deleteModal = document.getElementById('deleteAccountModal');
                    if (deleteModal) deleteModal.classList.remove('active');
                    confirmDeleteAccBtn.innerHTML = originalText;
                    confirmDeleteAccBtn.disabled = false;
                    const activeLink = document.querySelector('.nav-links li.active a');
                    if (activeLink) activeLink.click();
                })
                .catch(error => {
                    alert("حدث خطأ في الاتصال بالسيرفر!");
                    confirmDeleteAccBtn.innerHTML = originalText;
                    confirmDeleteAccBtn.disabled = false;
                });
                return;
            }

            /* ================= [ 5. عرض متبرعي الحالة ] ================= */
            const donorsBtn = e.target.closest('.btn-donors');
            if (donorsBtn) {
                const tableRow = donorsBtn.closest('tr');
                if (tableRow) {
                    const hospitalName = tableRow.cells[0].innerText;
                    const bloodTypeCell = tableRow.querySelector('.blood-type-text, .blood-badge, .status-info, .status-urgent, .status-badge');
                    let bloodType = bloodTypeCell ? bloodTypeCell.innerText : (tableRow.cells[1] ? tableRow.cells[1].innerText : "");
                    document.getElementById('modalCaseHospital').innerText = hospitalName;
                    document.getElementById('modalCaseBlood').innerText = bloodType;
                    document.getElementById('donorsModal').classList.add('active');
                }
                return;
            }
            const closeDonorsBtn = e.target.closest('#closeDonorsModalBtn');
            if (closeDonorsBtn || e.target.id === 'donorsModal') {
                document.getElementById('donorsModal').classList.remove('active');
                return;
            }

            /* ================= [ 6. رفض التبرع ] ================= */
            const rejectBtn = e.target.closest('.btn-reject');
            if (rejectBtn) {
                const donorCard = rejectBtn.closest('.donor-card');
                if (donorCard) {
                    const donorName = donorCard.querySelector('.donor-info h4').innerText;
                    const bloodType = donorCard.querySelector('.blood-type-text').innerText;
                    const donationId = rejectBtn.getAttribute('data-id'); 

                    document.getElementById('modalDonorName').innerText = donorName;
                    document.getElementById('modalDonorBlood').innerText = bloodType;
                    document.getElementById('rejectDonationIdInput').value = donationId; 
                    document.getElementById('rejectModal').classList.add('active');
                }
                return;
            }
            const closeRejectBtn = e.target.closest('#closeModalBtn') || e.target.closest('#cancelRejectBtn');
            if (closeRejectBtn || e.target.id === 'rejectModal') {
                document.getElementById('rejectModal').classList.remove('active');
                document.getElementById('rejectReasonSelect').selectedIndex = 0;
                return;
            }
            const confirmRejectBtn = e.target.closest('#confirmRejectBtn');
            if (confirmRejectBtn) {
                const selectedReason = document.getElementById('rejectReasonSelect').value;
                const donationId = document.getElementById('rejectDonationIdInput').value;

                if(selectedReason === "") {
                    alert("يرجى اختيار سبب الرفض أولاً!");
                    return;
                } 
                
                const originalText = confirmRejectBtn.innerHTML;
                confirmRejectBtn.innerHTML = "جاري الرفض...";
                confirmRejectBtn.disabled = true;

                const formData = new FormData();
                formData.append('donation_id', donationId);
                formData.append('reason', selectedReason);

                fetch('process_reject.php', { method: 'POST', body: formData })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    document.getElementById('rejectModal').classList.remove('active');
                    document.getElementById('rejectReasonSelect').selectedIndex = 0;
                    confirmRejectBtn.innerHTML = originalText;
                    confirmRejectBtn.disabled = false;
                    document.querySelector('.nav-links a[href="records.php"]').click();
                })
                .catch(error => {
                    alert("حدث خطأ في الاتصال بالسيرفر!");
                    confirmRejectBtn.innerHTML = originalText;
                    confirmRejectBtn.disabled = false;
                });
                return;
            }

            /* ================= [ 7. تأكيد التبرع (قبول) ] ================= */
            const acceptBtn = e.target.closest('.btn-accept');
            if (acceptBtn) {
                const donorCard = acceptBtn.closest('.donor-card');
                const rejectBtnInCard = donorCard.querySelector('.btn-reject'); 
                const donationId = acceptBtn.getAttribute('data-id');

                if (!confirm("هل أنت متأكد من تأكيد تبرع هذه الحالة بنجاح؟")) return;

                if (rejectBtnInCard) rejectBtnInCard.style.display = 'none';

                const originalText = acceptBtn.innerHTML;
                acceptBtn.innerHTML = "جاري التأكيد...";
                acceptBtn.disabled = true;

                const formData = new FormData();
                formData.append('donation_id', donationId);

                fetch('process_accept.php', { method: 'POST', body: formData })
                .then(response => response.text())
                .then(data => {
                    alert(data); 
                    acceptBtn.innerHTML = '<i class="fa-solid fa-check-double"></i> تم التأكيد';
                    acceptBtn.style.backgroundColor = '#d0edd3'; 
                    acceptBtn.style.color = '#2e7d32';
                    acceptBtn.style.cursor = 'default';

                    setTimeout(() => {
                        document.querySelector('.nav-links a[href="records.php"]').click();
                    }, 1500); 
                })
                .catch(error => {
                    alert("حدث خطأ في الاتصال بالسيرفر!");
                    acceptBtn.innerHTML = originalText;
                    acceptBtn.disabled = false;
                    if (rejectBtnInCard) rejectBtnInCard.style.display = 'flex';
                });
                return;
            }

            /* ================= [ 8. إضافة مركز جديد (المراكز) ] ================= */
            const addNewCenterBtn = e.target.closest('#addNewCenterBtn');
            if (addNewCenterBtn) {
                const tbody = document.querySelector('.centers-page-container tbody');
                if (!tbody) return;
                
                if (tbody.querySelector('.adding-new-row')) return; 

                const emptyRow = tbody.querySelector('td[colspan="10"]');
                if (emptyRow) emptyRow.closest('tr').remove();

                const newRow = document.createElement('tr');
                newRow.className = 'adding-new-row editing';
                newRow.innerHTML = `
                    <td style="color: #777; font-weight: bold;">جديد</td>
                    <td><input type="text" class="edit-input input-name" placeholder="اسم المركز"></td>
                    <td><input type="text" class="edit-input input-desc" placeholder="الوصف"></td>
                    <td>
                        <select class="edit-select select-type">
                            <option value="hospital">مستشفى</option>
                            <option value="blood_bank">بنك دم</option>
                        </select>
                    </td>
                    <td><input type="text" class="edit-input input-phone" placeholder="رقم الهاتف" dir="ltr" style="text-align: right;"></td>
                    <td dir="ltr" style="text-align: right; color: #777; font-size: 11px;">الآن</td>
                    <td><input type="text" class="edit-input input-lat" placeholder="مثال: 15.50" dir="ltr" style="text-align: right;"></td>
                    <td><input type="text" class="edit-input input-long" placeholder="مثال: 32.56" dir="ltr" style="text-align: right;"></td>
                    <td>
                        <select class="edit-select select-status">
                            <option value="مفتوح" selected>مفتوح</option>
                            <option value="مغلق">مغلق</option>
                        </select>
                    </td>
                    <td>
                        <div class="admin-actions">
                            <button class="btn-save center-save-new-btn" title="حفظ"><i class="fa-solid fa-check"></i> حفظ</button>
                            <button class="btn-cancel center-cancel-add-btn" title="إلغاء"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </td>
                `;
                tbody.insertBefore(newRow, tbody.firstChild);
                return;
            }

            const cancelAddBtn = e.target.closest('.center-cancel-add-btn');
            if (cancelAddBtn) {
                document.querySelector('.nav-links a[href="centers.php"]').click();
                return;
            }

            const saveNewBtn = e.target.closest('.center-save-new-btn');
            if (saveNewBtn) {
                const row = saveNewBtn.closest('tr');
                const nameInput = row.querySelector('.input-name').value.trim();
                
                if (!nameInput) {
                    alert("يرجى كتابة اسم المركز أولاً!");
                    return;
                }

                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('name', nameInput);
                formData.append('description', row.querySelector('.input-desc').value);
                formData.append('type', row.querySelector('.select-type').value);
                formData.append('phone', row.querySelector('.input-phone').value);
                formData.append('latitude', row.querySelector('.input-lat').value);
                formData.append('longitude', row.querySelector('.input-long').value);
                formData.append('status', row.querySelector('.select-status').value);

                const originalText = saveNewBtn.innerHTML;
                saveNewBtn.innerHTML = "جاري...";
                saveNewBtn.disabled = true;

                fetch('centers.php', { method: 'POST', body: formData })
                .then(res => res.text())
                .then(data => {
                    alert(data);
                    document.querySelector('.nav-links a[href="centers.php"]').click();
                }).catch(err => {
                    alert('حدث خطأ في الاتصال بالسيرفر!');
                    saveNewBtn.innerHTML = originalText;
                    saveNewBtn.disabled = false;
                });
                return;
            }

            /* ================= [ 9. إدارة المراكز (تعديل) ] ================= */
            const centerEditBtn = e.target.closest('.center-edit-btn');
            if (centerEditBtn) {
                const row = centerEditBtn.closest('tr');
                
                const id = row.getAttribute('data-id');
                const name = row.getAttribute('data-name');
                const desc = row.getAttribute('data-description');
                const type = row.getAttribute('data-type');
                const phone = row.getAttribute('data-phone');
                const lat = row.getAttribute('data-latitude');
                const long = row.getAttribute('data-longitude');
                const status = row.getAttribute('data-status');

                row.querySelector('.cell-name').innerHTML = `<input type="text" class="edit-input input-name" value="${name}">`;
                row.querySelector('.cell-desc').innerHTML = `<input type="text" class="edit-input input-desc" value="${desc}">`;
                
                row.querySelector('.cell-type').innerHTML = `
                    <select class="edit-select select-type">
                        <option value="hospital" ${type === 'hospital' ? 'selected' : ''}>مستشفى</option>
                        <option value="blood_bank" ${type === 'blood_bank' ? 'selected' : ''}>بنك دم</option>
                    </select>`;

                row.querySelector('.cell-phone').innerHTML = `<input type="text" class="edit-input input-phone" value="${phone}">`;
                row.querySelector('.cell-lat').innerHTML = `<input type="text" class="edit-input input-lat" value="${lat}">`;
                row.querySelector('.cell-long').innerHTML = `<input type="text" class="edit-input input-long" value="${long}">`;

                row.querySelector('.cell-status').innerHTML = `
                    <select class="edit-select select-status">
                        <option value="مفتوح" ${status === 'مفتوح' ? 'selected' : ''}>مفتوح</option>
                        <option value="مغلق" ${status === 'مغلق' ? 'selected' : ''}>مغلق</option>
                    </select>`;

                row.querySelector('.admin-actions').innerHTML = `
                    <button class="btn-save center-save-btn" title="حفظ"><i class="fa-solid fa-check"></i> حفظ</button>
                    <button class="btn-cancel center-cancel-btn" title="إلغاء"><i class="fa-solid fa-xmark"></i></button>
                `;
                return;
            }

            const centerCancelBtn = e.target.closest('.center-cancel-btn');
            if (centerCancelBtn) {
                document.querySelector('.nav-links a[href="centers.php"]').click();
                return;
            }

            const centerSaveBtn = e.target.closest('.center-save-btn');
            if (centerSaveBtn) {
                const row = centerSaveBtn.closest('tr');
                const id = row.getAttribute('data-id');

                const formData = new FormData();
                formData.append('action', 'edit');
                formData.append('center_id', id);
                formData.append('name', row.querySelector('.input-name').value);
                formData.append('description', row.querySelector('.input-desc').value);
                formData.append('type', row.querySelector('.select-type').value);
                formData.append('phone', row.querySelector('.input-phone').value);
                formData.append('latitude', row.querySelector('.input-lat').value);
                formData.append('longitude', row.querySelector('.input-long').value);
                formData.append('status', row.querySelector('.select-status').value);

                centerSaveBtn.innerHTML = "جاري الحفظ...";
                centerSaveBtn.disabled = true;

                fetch('centers.php', { method: 'POST', body: formData })
                .then(res => res.text())
                .then(data => {
                    alert(data);
                    document.querySelector('.nav-links a[href="centers.php"]').click();
                }).catch(err => {
                    alert('حدث خطأ في الاتصال بالسيرفر!');
                    centerSaveBtn.innerHTML = '<i class="fa-solid fa-check"></i> حفظ';
                    centerSaveBtn.disabled = false;
                });
                return;
            }

            /* ================= [ 10. إدارة المراكز (حذف) ] ================= */
            const centerDeleteBtn = e.target.closest('.center-delete-btn');
            if (centerDeleteBtn) {
                const row = centerDeleteBtn.closest('tr');
                const id = row.getAttribute('data-id');

                if (confirm('هل أنت متأكد تماماً من حذف هذا المركز وكافة الطلبات المرتبطة به بشكل نهائي؟')) {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('center_id', id);

                    fetch('centers.php', { method: 'POST', body: formData })
                    .then(res => res.text())
                    .then(data => {
                        alert(data);
                        document.querySelector('.nav-links a[href="centers.php"]').click();
                    }).catch(err => {
                        alert('حدث خطأ في الاتصال بالسيرفر!');
                    });
                }
                return;
            }

            /* ================= [ 11. إضافة نداء استغاثة (الصفحة الرئيسية) ] ================= */
            const submitRequestBtn = e.target.closest('#submitRequestBtn');
            if (submitRequestBtn) {
                e.preventDefault(); 
                const centerId = document.getElementById('req_center_id').value;
                const bloodType = document.getElementById('req_blood_type').value;
                const priority = document.getElementById('req_priority').value;
                const caseDesc = document.getElementById('req_case_description').value.trim();

                if (!centerId || !caseDesc) {
                    alert("يرجى اختيار المستشفى وكتابة وصف الحالة!");
                    return;
                }

                const originalText = submitRequestBtn.innerHTML;
                submitRequestBtn.innerHTML = "جاري النشر...";
                submitRequestBtn.disabled = true;

                const formData = new FormData();
                formData.append('action', 'add_request');
                formData.append('center_id', centerId);
                formData.append('blood_type', bloodType);
                formData.append('priority', priority);
                formData.append('case_description', caseDesc);

                fetch('home.php', { method: 'POST', body: formData })
                .then(res => res.text())
                .then(data => {
                    alert(data);
                    document.querySelector('.nav-links a[href="home.php"]').click();
                }).catch(err => {
                    alert('حدث خطأ في الاتصال!');
                    submitRequestBtn.innerHTML = originalText;
                    submitRequestBtn.disabled = false;
                });
                return;
            }

            /* ================= [ 12. تعديل نداء استغاثة (الصفحة الرئيسية) ] ================= */
            const reqEditBtn = e.target.closest('.req-edit-btn');
            if (reqEditBtn) {
                const row = reqEditBtn.closest('tr');
                const id = row.getAttribute('data-id');
                const centerId = row.getAttribute('data-center');
                const blood = row.getAttribute('data-blood');
                const priority = row.getAttribute('data-priority');
                const desc = row.getAttribute('data-desc');

                const centersOptions = document.getElementById('centersOptionsData').innerHTML;

                row.querySelector('.cell-center').innerHTML = `<select class="edit-mode-input req-edit-center">${centersOptions}</select>`;
                row.querySelector('.req-edit-center').value = centerId; 

                row.querySelector('.cell-blood').innerHTML = `
                    <select class="edit-mode-input req-edit-blood">
                        <option value="O-" ${blood==='O-'?'selected':''}>O-</option>
                        <option value="O+" ${blood==='O+'?'selected':''}>O+</option>
                        <option value="A-" ${blood==='A-'?'selected':''}>A-</option>
                        <option value="A+" ${blood==='A+'?'selected':''}>A+</option>
                        <option value="B-" ${blood==='B-'?'selected':''}>B-</option>
                        <option value="B+" ${blood==='B+'?'selected':''}>B+</option>
                        <option value="AB-" ${blood==='AB-'?'selected':''}>AB-</option>
                        <option value="AB+" ${blood==='AB+'?'selected':''}>AB+</option>
                    </select>
                `;

                row.querySelector('.cell-priority').innerHTML = `
                    <select class="edit-mode-input req-edit-priority">
                        <option value="critical" ${priority==='critical'?'selected':''}>خطيرة جداً</option>
                        <option value="warning" ${priority==='warning'?'selected':''}>متوسطة</option>
                        <option value="stable" ${priority==='stable'?'selected':''}>مستقرة</option>
                        <option value="normal" ${priority==='normal'?'selected':''}>روتيني</option>
                    </select>
                `;

                row.querySelector('.cell-desc').innerHTML = `<input type="text" class="edit-mode-input req-edit-desc" value="${desc}">`;

                row.querySelector('.table-actions').innerHTML = `
                    <button class="btn-action-sm btn-save req-save-btn" title="حفظ"><i class="fa-solid fa-check"></i></button>
                    <button class="btn-action-sm btn-cancel req-cancel-btn" title="إلغاء"><i class="fa-solid fa-xmark"></i></button>
                `;
                return;
            }

            const reqCancelBtn = e.target.closest('.req-cancel-btn');
            if (reqCancelBtn) {
                document.querySelector('.nav-links a[href="home.php"]').click();
                return;
            }

            const reqSaveBtn = e.target.closest('.req-save-btn');
            if (reqSaveBtn) {
                const row = reqSaveBtn.closest('tr');
                const id = row.getAttribute('data-id');
                const centerId = row.querySelector('.req-edit-center').value;
                const bloodType = row.querySelector('.req-edit-blood').value;
                const priority = row.querySelector('.req-edit-priority').value;
                const caseDesc = row.querySelector('.req-edit-desc').value;

                reqSaveBtn.innerHTML = "...";
                reqSaveBtn.disabled = true;

                const formData = new FormData();
                formData.append('action', 'edit_request');
                formData.append('request_id', id);
                formData.append('center_id', centerId);
                formData.append('blood_type', bloodType);
                formData.append('priority', priority);
                formData.append('case_description', caseDesc);

                fetch('home.php', { method: 'POST', body: formData })
                .then(res => res.text())
                .then(data => {
                    alert(data);
                    document.querySelector('.nav-links a[href="home.php"]').click();
                }).catch(err => {
                    alert('حدث خطأ في الاتصال!');
                    reqSaveBtn.innerHTML = '<i class="fa-solid fa-check"></i>';
                    reqSaveBtn.disabled = false;
                });
                return;
            }

            /* ================= [ 13. حذف نداء استغاثة (الصفحة الرئيسية) ] ================= */
            const reqDeleteBtn = e.target.closest('.req-delete-btn');
            if (reqDeleteBtn) {
                if (confirm('هل أنت متأكد من حذف نداء الاستغاثة هذا نهائياً؟')) {
                    const row = reqDeleteBtn.closest('tr');
                    const id = row.getAttribute('data-id');

                    const formData = new FormData();
                    formData.append('action', 'delete_request');
                    formData.append('request_id', id);

                    fetch('home.php', { method: 'POST', body: formData })
                    .then(res => res.text())
                    .then(data => {
                        alert(data);
                        document.querySelector('.nav-links a[href="home.php"]').click();
                    }).catch(err => {
                        alert('حدث خطأ في الاتصال!');
                    });
                }
                return;
            }

        });
    });
    </script>
</body>
</html>