<style>
    /* ── Change Password Modal ── */
    #settings_modal .modal-content {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 16px 48px rgba(0,49,102,.14);
    }
    #settings_modal .modal-body { padding: 0; }
    .cp-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        background: #f2f4f6;
        border-bottom: 1px solid #c2c6d2;
    }
    .cp-title {
        font-family: 'Manrope', sans-serif;
        font-size: 16px;
        font-weight: 700;
        color: #003166;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .cp-close {
        background: transparent;
        border: none;
        color: #737782;
        font-size: 16px;
        padding: 5px 8px;
        border-radius: 8px;
        cursor: pointer;
        transition: background .12s;
        line-height: 1;
    }
    .cp-close:hover { background: #eceef0; color: #191c1e; }
    .cp-body { padding: 20px; }
    .cp-error {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        background: #ffdad6;
        color: #ba1a1a;
        font-size: 13px;
        font-weight: 500;
        padding: 11px 14px;
        border-radius: 10px;
        margin-bottom: 16px;
    }
    .cp-error .error_list {
        margin: 4px 0 0;
        padding-left: 16px;
        font-size: 12px;
    }
    .cp-label {
        font-size: 12px;
        font-weight: 600;
        color: #424751;
        margin-bottom: 5px;
        display: block;
    }
    .cp-input {
        width: 100%;
        height: 42px;
        border: 1px solid #c2c6d2;
        border-radius: 10px;
        padding: 0 14px;
        font-size: 13px;
        color: #191c1e;
        background: #f2f4f6;
        font-family: 'Inter', sans-serif;
        outline: none;
        transition: border-color .15s, background .15s, box-shadow .15s;
    }
    .cp-input:focus {
        border-color: #00478f;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(0,71,143,.1);
    }
    .cp-field { margin-bottom: 14px; }
    .cp-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        padding: 14px 20px;
        border-top: 1px solid #eceef0;
        background: #f7f9fb;
    }
    .cp-btn-cancel {
        background: transparent;
        border: 1px solid #c2c6d2;
        color: #424751;
        font-family: 'Manrope', sans-serif;
        font-size: 13px;
        font-weight: 600;
        padding: 9px 18px;
        border-radius: 9px;
        cursor: pointer;
        transition: background .12s;
    }
    .cp-btn-cancel:hover { background: #eceef0; }
    .cp-btn-submit {
        background: #003166;
        color: #fff;
        border: none;
        font-family: 'Manrope', sans-serif;
        font-size: 13px;
        font-weight: 700;
        padding: 9px 22px;
        border-radius: 9px;
        cursor: pointer;
        transition: opacity .15s, transform .1s;
    }
    .cp-btn-submit:hover  { opacity: .88; }
    .cp-btn-submit:active { transform: scale(.97); }
</style>

<div class="modal fade" id="settings_modal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content">
            <div class="modal-body">

                <!-- Header -->
                <div class="cp-header">
                    <h5 class="cp-title">
                        <i class="bi bi-key-fill"></i> Change Password
                    </h5>
                    <button type="button" class="cp-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Form body -->
                <div class="cp-body">

                    <!-- Error container -->
                    <div class="cp-error d-none error_container">
                        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                        <div>
                            Please fix the following:
                            <ul class="error_list mb-0"></ul>
                        </div>
                    </div>

                    <div class="cp-field">
                        <label class="cp-label" for="old_password">Current Password</label>
                        <input type="password" class="cp-input" id="old_password" placeholder="Enter current password">
                    </div>
                    <div class="cp-field">
                        <label class="cp-label" for="new_password">New Password</label>
                        <input type="password" class="cp-input" id="new_password" placeholder="Enter new password">
                    </div>
                    <div class="cp-field" style="margin-bottom:0">
                        <label class="cp-label" for="confirm_password">Confirm New Password</label>
                        <input type="password" class="cp-input" id="confirm_password" placeholder="Re-enter new password">
                    </div>

                </div>

                <!-- Footer -->
                <div class="cp-footer">
                    <button type="button" class="cp-btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="cp-btn-submit" id="btn_change_password">
                        <i class="bi bi-check2-circle me-1"></i> Update Password
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
