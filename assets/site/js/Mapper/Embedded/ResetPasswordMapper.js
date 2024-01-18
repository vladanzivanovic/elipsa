class ResetPasswordMapper {
    constructor() {
        if (!ResetPasswordMapper.instance) {
            this.form = '#reset_form',
            this.resetPwdFormWrapper = '#reset_password_form_wrapper',
            this.resetPasswordBtn = '#reset_password_btn',
            this.resetPasswordSubmitBtn = '#reset_btn',

            ResetPasswordMapper.instance = this;
        }

        return ResetPasswordMapper.instance;
    }

}

const resetPasswordMapper = new ResetPasswordMapper();

Object.freeze(resetPasswordMapper);

export default resetPasswordMapper;
