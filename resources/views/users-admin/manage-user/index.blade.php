@extends('layouts.app')

@section('title', 'Users List')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/user-management.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        #registrationForm {
            display: none;
        }

        #studentFields {
            display: none;
            padding-top: 10px;
            padding-bottom: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 3px solid #6777ef;
            margin-bottom: 15px;
            padding-left: 15px;
            padding-right: 15px;
        }

        #studentFields h6 {
            color: #6777ef;
            margin-bottom: 15px;
        }

        /* Custom validation styles */
        .validation-field.is-valid {
            border-color: #28a745;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .validation-field.is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23dc3545' viewBox='-2 -2 7 7'%3e%3cpath stroke='%23dc3545' d='M0 0l3 3m0-3L0 3'/%3e%3ccircle r='.5'/%3e%3ccircle cx='3' r='.5'/%3e%3ccircle cy='3' r='.5'/%3e%3ccircle cx='3' cy='3' r='.5'/%3e%3c/svg%3E");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .validation-message i {
            transition: all 0.3s ease;
        }

        .validation-message.text-success i {
            content: "\f00c";
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1 style="font-size: 21px;">Users List</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Users</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Users Data</h4>
                                <button type="button" class="btn btn-primary" id="addUserBtn">
                                    <i class="fas fa-plus"></i> Add User
                                </button>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-1 control-label col-form-label">Filter by Role:</label>
                                            <div class="col-3">
                                                <select class="form-control" id="filter_role" name="role">
                                                    <option value="">- All Roles -</option>
                                                    <option value="student">Student</option>
                                                    <option value="lecturer">Lecturer</option>
                                                    <option value="staff">Staff</option>
                                                    <option value="alumni">Alumni</option>
                                                </select>
                                                <small class="form-text text-muted">Select Role</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="registrationForm">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Add New User</h4>
                                        </div>
                                        <div class="card-body">
                                            @if ($errors->any())
                                                <div class="alert alert-danger">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            <form method="POST" action="{{ route('users.store') }}">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="name">Full Name</label>
                                                    <input type="text" class="form-control validation-field" id="name" name="name" required
                                                        data-validation="name" pattern="[A-Za-z\s]+"
                                                        title="Only letters and spaces are allowed">
                                                    <small class="form-text text-muted validation-message" id="name-validation">
                                                        <i class="fas fa-info-circle mr-1"></i> Only letters and spaces allowed, minimum 3
                                                        characters
                                                    </small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="role">Role</label>
                                                    <select class="form-control validation-field" id="role" name="role" required
                                                        data-validation="required">
                                                        <option value="">Select Role</option>
                                                        @foreach (['student', 'lecturer', 'staff', 'alumni', 'admin'] as $role)
                                                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                                        @endforeach
                                                    </select>
                                                    <small class="form-text text-muted validation-message" id="role-validation">
                                                        <i class="fas fa-info-circle mr-1"></i> Required field - selects user permissions
                                                    </small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="identity_number">Identity Number</label>
                                                    <input type="text" class="form-control validation-field" id="identity_number"
                                                        name="identity_number" required data-validation="identity">
                                                    <small class="form-text text-muted validation-message" id="identity_number-validation">
                                                        <i class="fas fa-info-circle mr-1"></i> Enter NIM for students or employee ID for
                                                        staff/lecturers
                                                    </small>
                                                </div>

                                                <div id="studentFieldsContainer">
                                                    <div id="studentFields" style="display: none;">
                                                        <h6><i class="fas fa-user-graduate mr-2"></i>Student Information</h6>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="major">Major</label>
                                                                    <select class="form-control validation-field" id="major" name="major"
                                                                        data-validation="required">
                                                                        <option value="">Select Major</option>
                                                                        <option value="Teknik Elektro">Teknik Elektro</option>
                                                                        <option value="Teknik Sipil">Teknik Sipil</option>
                                                                        <option value="Teknik Mesin">Teknik Mesin</option>
                                                                        <option value="Teknik Kimia">Teknik Kimia</option>
                                                                        <option value="Akuntansi">Akuntansi</option>
                                                                        <option value="Administrasi Niaga">Administrasi Niaga</option>
                                                                        <option value="Teknologi Informasi">Teknologi Informasi</option>
                                                                    </select>
                                                                    <small class="form-text validation-message" id="major-validation">
                                                                        <i class="fas fa-info-circle mr-1"></i> Required for students
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="study_program">Study Program</label>
                                                                    <select class="form-control validation-field" id="study_program"
                                                                        name="study_program" data-validation="required">
                                                                        <option value="">Select Study Program</option>
                                                                    </select>
                                                                    <small class="form-text validation-message" id="study_program-validation">
                                                                        <i class="fas fa-info-circle mr-1"></i> Will appear after selecting a
                                                                        major
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="campus">Campus</label>
                                                                    <select class="form-control validation-field" id="campus" name="campus"
                                                                        data-validation="required">
                                                                        <option value="">Select Campus</option>
                                                                        <option value="malang">Malang</option>
                                                                        <option value="psdku_kediri">PSDKU Kediri</option>
                                                                        <option value="psdku_lumajang">PSDKU Lumajang</option>
                                                                        <option value="psdku_pamekasan">PSDKU Pamekasan</option>
                                                                    </select>
                                                                    <small class="form-text validation-message" id="campus-validation">
                                                                        <i class="fas fa-info-circle mr-1"></i> Required for students
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="password">Password</label>
                                                    <input type="password" class="form-control validation-field" id="password" name="password"
                                                        required data-validation="password">
                                                    <small class="form-text text-muted validation-message" id="password-validation">
                                                        <i class="fas fa-lock mr-1"></i> Must be at least 8 characters with letters and numbers
                                                    </small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="password_confirmation">Confirm Password</label>
                                                    <input type="password" class="form-control validation-field" id="password_confirmation"
                                                        name="password_confirmation" required data-validation="password-match">
                                                    <small class="form-text text-muted validation-message"
                                                        id="password_confirmation-validation">
                                                        <i class="fas fa-check-circle mr-1"></i> Must match your password exactly
                                                    </small>
                                                </div>
                                                <div class="form-group text-right">
                                                    <button type="button" class="btn btn-secondary mr-1" id="cancelBtn">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover table-sm" id="table_user">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Role</th>
                                                <th>Identity Number</th>
                                                <th>Name</th>
                                                <th>Exam Status</th>
                                                <th>Certificate Status</th>
                                                <th>KTP</th>
                                                <th>Photo</th>
                                                <th>Home Address</th>
                                                <th>Current Address</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" aria-hidden="true"></div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/numeric-input-validation.js') }}"></script>
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function () {
                $('#myModal').modal('show');
                initializeValidation();
            });
        }

        function validateName(value) {
            const nameRegex = /^[A-Za-z\s]+$/;
            return value.length >= 3 && nameRegex.test(value);
        }

        function validateIdentity(value) {
            return value.length >= 5;
        }

        function validatePassword(value) {
            return value.length >= 8 && /[A-Za-z]/.test(value) && /[0-9]/.test(value);
        }

        function validateRequired(value) {
            return value !== "" && value !== null;
        }

        function validateField(field) {
            const value = field.val();
            const validationType = field.data('validation');
            let isValid = false;

            if (value === "" && !field.hasClass('form-submitted')) {
                field.removeClass('is-valid is-invalid');
                updateValidationMessage(field, 'info');
                return true;
            }

            switch (validationType) {
                case 'name':
                    isValid = validateName(value);
                    break;
                case 'identity':
                    isValid = validateIdentity(value);
                    break;
                case 'password':
                    isValid = validatePassword(value);
                    break;
                case 'password-match':
                    isValid = value === $('#password').val();
                    break;
                case 'required':
                    isValid = validateRequired(value);
                    break;
                default:
                    isValid = true;
            }

            if (isValid) {
                field.removeClass('is-invalid').addClass('is-valid');
                updateValidationMessage(field, 'success');
            } else {
                field.removeClass('is-valid').addClass('is-invalid');
                updateValidationMessage(field, 'danger');
            }

            return isValid;
        }

        function updateValidationMessage(field, status) {
            const feedbackId = field.attr('id') + '-validation';
            const feedbackElement = $('#' + feedbackId);
            const icon = feedbackElement.find('i');

            if (feedbackElement.length) {
                feedbackElement.removeClass('text-success text-danger text-muted');
                icon.removeClass('fa-check-circle fa-times-circle fa-info-circle');

                if (status === 'success') {
                    feedbackElement.addClass('text-success');
                    icon.addClass('fa-check-circle');
                } else if (status === 'danger') {
                    feedbackElement.addClass('text-danger');
                    icon.addClass('fa-times-circle');
                } else {
                    feedbackElement.addClass('text-muted');
                    icon.addClass('fa-info-circle');
                }
            }
        }

        function initializeValidation() {
            $('.validation-field').off('input change').on('input change', function () {
                validateField($(this));
            });

            $('#name').off('keypress').on('keypress', function (e) {
                const char = String.fromCharCode(e.which);
                const nameRegex = /^[A-Za-z\s]+$/;
                if (!nameRegex.test(char)) {
                    e.preventDefault();
                    return false;
                }
            });

            $('#name').off('paste').on('paste', function (e) {
                setTimeout(() => {
                    const value = $(this).val();
                    const nameRegex = /^[A-Za-z\s]*$/;
                    if (!nameRegex.test(value)) {
                        $(this).val(value.replace(/[^A-Za-z\s]/g, ''));
                        validateField($(this));
                    }
                }, 10);
            });

            $('#password').off('input change').on('input change', function () {
                validateField($(this));
                if ($('#password_confirmation').val() !== '') {
                    validateField($('#password_confirmation'));
                }
            });

            $('form').off('reset').on('reset', function () {
                $(this).find('.validation-field').removeClass('is-valid is-invalid');
                $(this).find('.validation-message')
                    .removeClass('text-success text-danger')
                    .addClass('text-muted');
                $(this).find('.validation-message i')
                    .removeClass('fa-check-circle fa-times-circle')
                    .addClass('fa-info-circle');
            });

            $('form').off('submit').on('submit', function (e) {
                const form = $(this);
                const role = $('#role').val();

                if (role !== 'student') {
                    $('#studentFieldsContainer').empty();
                }

                form.find('.validation-field').addClass('form-submitted');

                let isValid = true;
                let errorMessages = [];

                form.find('.validation-field').each(function () {
                    if (!validateField($(this))) {
                        isValid = false;
                        const fieldId = $(this).attr('id');
                        const fieldLabel = $('label[for="' + fieldId + '"]').text();
                        errorMessages.push(`${fieldLabel} is invalid.`);
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: errorMessages.join('<br>') || 'Please check all fields and try again.'
                    });
                    return false;
                }
            });
        }

        $(document).ready(function () {
            initializeValidation();

            var dataUser = $('#table_user').DataTable({
                serverSide: true,
                ajax: {
                    url: "{{ url('users/list') }}",
                    type: "POST",
                    dataType: "json",
                    data: function (d) {
                        d._token = $('meta[name="csrf-token"]').attr('content');
                        d.role = $('#filter_role').val();
                    }
                },
                columns: [
                    {
                        data: "user_id",
                        className: "text-center",
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: "role",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "identity_number",
                        className: "",
                        width: "12%",
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: "name",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "exam_status",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "certificate_status",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "ktp_scan",
                        className: "",
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return data && data !== '-' ? '<a href="' + data + '" target="_blank">KTP</a>' : '-';
                        }
                    },
                    {
                        data: "photo",
                        className: "",
                        width: "5%",
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return data && data !== '-' ? '<a href="' + data + '" target="_blank">Photo</a>' : '-';
                        }
                    },
                    {
                        data: "home_address",
                        className: "",
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: "current_address",
                        className: "",
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: "action",
                        className: "",
                        width: "10%",
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [[0, 'desc']],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ],
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
            });

            $('#filter_role').on('change', function () {
                dataUser.ajax.reload(); 
            });

            const studyPrograms = {
                'Teknik Elektro': [
                    'D-III Teknik Elektronika (Akreditasi Unggul)',
                    'D-III Teknik Listrik (Akreditasi Unggul)',
                    'D-III Teknik Telekomunikasi',
                    'D-IV Teknik Elektronika',
                    'D-IV Sistem Kelistrikan',
                    'D-IV Jaringan Telekomunikasi Digital',
                    'D-IV Teknik Elektronika (PSDKU Kediri)'
                ],
                'Teknik Sipil': [
                    'D-III Teknik Sipil',
                    'D-IV Teknik Sipil'
                ],
                'Teknik Mesin': [
                    'D-III Teknik Mesin',
                    'D-IV Teknik Mesin'
                ],
                'Teknik Kimia': [
                    'D-III Teknik Kimia',
                    'D-IV Teknik Kimia'
                ],
                'Akuntansi': [
                    'D-III Akuntansi',
                    'D-IV Akuntansi Manajemen'
                ],
                'Administrasi Niaga': [
                    'D-III Administrasi Bisnis',
                    'D-IV Manajemen Pemasaran'
                ],
                'Teknologi Informasi': [
                    'D-IV Sistem Informasi Bisnis',
                    'D-IV Teknik Informatika'
                ]
            };

            $('#addUserBtn').on('click', function () {
                $('#registrationForm').show();
                $(this).hide();

                $('form')[0].reset();
                $('.validation-field').removeClass('is-valid is-invalid form-submitted');
                $('.validation-message')
                    .removeClass('text-success text-danger')
                    .addClass('text-muted');
                $('.validation-message i')
                    .removeClass('fa-check-circle fa-times-circle')
                    .addClass('fa-info-circle');

                const role = $('#role').val();
                if (role === 'student') {
                    $('#studentFieldsContainer').empty().append(studentFieldsTemplate.clone());
                    $('#studentFields').show();
                    bindStudentFieldsEvents();
                } else {
                    $('#studentFieldsContainer').empty();
                }
            });

            $('#cancelBtn').on('click', function () {
                $('#registrationForm').hide();
                $('#addUserBtn').show();
                $('form')[0].reset();
                $('.validation-field').removeClass('is-valid is-invalid form-submitted');
                $('.validation-message')
                    .removeClass('text-success text-danger')
                    .addClass('text-muted');
                $('.validation-message i')
                    .removeClass('fa-check-circle fa-times-circle')
                    .addClass('fa-info-circle');
                $('#studentFields').hide();
            });

            const studentFieldsTemplate = $('#studentFields').clone();

            $('#role').on('change', function () {
                const studentFieldsContainer = $('#studentFieldsContainer');

                if ($(this).val() === 'student') {
                    studentFieldsContainer.empty().append(studentFieldsTemplate.clone());
                    const studentFields = $('#studentFields');
                    studentFields.show();

                    bindStudentFieldsEvents();

                    studentFields.css('opacity', '0');
                    setTimeout(() => {
                        studentFields.css({
                            'transition': 'opacity 0.3s',
                            'opacity': '1'
                        });
                    }, 10);
                } else {
                    studentFieldsContainer.empty();
                }
            });

            function bindStudentFieldsEvents() {
                $('#major').off('change').on('change', function () {
                    const studyProgramSelect = $('#study_program');
                    const selectedMajor = $(this).val();

                    studyProgramSelect.html('<option value="">Select Study Program</option>');

                    if (selectedMajor && studyPrograms[selectedMajor]) {
                        studyPrograms[selectedMajor].forEach(program => {
                            studyProgramSelect.append(`<option value="${program}">${program}</option>`);
                        });
                    }

                    validateField($(this));
                });

                $('#study_program, #campus').off('change').on('change', function () {
                    validateField($(this));
                });
            }

            $(document).on('submit', 'form', function (e) {
                e.preventDefault();

                const form = $(this);
                const role = $('#role').val();

                if (role !== 'student') {
                    $('#studentFieldsContainer').empty();
                }

                form.find('.validation-field').addClass('form-submitted');

                let isValid = true;
                let errorMessages = [];

                form.find('.validation-field').each(function () {
                    if (!validateField($(this))) {
                        isValid = false;
                        const fieldId = $(this).attr('id');
                        const fieldLabel = $('label[for="' + fieldId + '"]').text();
                        errorMessages.push(`${fieldLabel} is invalid.`);
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: errorMessages.join('<br>') || 'Please check all fields and try again.'
                    });
                    return false;
                }

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: new FormData(form[0]),
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.status === true) {
                            $('#registrationForm').hide();
                            $('#addUserBtn').show();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                dataUser.ajax.reload();
                                form[0].reset();
                                $('.validation-field').removeClass('is-valid is-invalid form-submitted');
                                $('.validation-message')
                                    .removeClass('text-success text-danger')
                                    .addClass('text-muted');
                                $('.validation-message i')
                                    .removeClass('fa-check-circle fa-times-circle')
                                    .addClass('fa-info-circle');
                                $('#studentFields').hide();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'An error occurred'
                            });
                        }
                    },
                });
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>
@endpush