<div class="x-panel">
    <div class="x_title">
        <h2>Add Membership</h2>

        <div class="clearfix"></div>
    </div>

    <div class="x_content" style="margin-top: 40px;">
        <div id="mainContent" style="display: block;">
            <form name="mainForm" id="mainForm" class="form-horizontal" method="post" action="<?= base_url('membership/save') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="form_name" value="mainForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="memberName" class="control-label col-md-3">Member Name*</label>
                            <input type="text" name="member_name" id="memberName" class="form-control" maxlength="100" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="birthDate" class="control-label col-md-3">Date of Birth*</label>
                            <input class="form-control" type="date" name="birth_date" id="birthDate" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="instName" class="control-label col-md-3">Institution*</label>
                            <input type="text" name="inst_name" id="instName" class="form-control" maxlength="256" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sinceDate" class="control-label col-md-3">Member Since*</label>
                            <input class="form-control" type="date" name="member_since_date" id="sinceDate" value="" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="memberTypeID" class="control-label col-md-3">Membership Type*</label>
                            <select name="member_type_id" id="memberTypeID" class="form-control" required>
                                <option value="1">Standard</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="regDate" class="control-label col-md-3">Registration Date*</label>
                            <input class="form-control" type="date" name="register_date" id="regDate" value="" required>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="memberPIN" class="control-label col-md-3">Identification Number*</label>
                            <input type="text" name="pin" id="memberPIN" class="form-control" maxlength="256" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3">Valid Until*</label>
                            <div class="checkbox">
                                <label><input type="checkbox" name="extend[]" value="1" checked=""> Automatic Settings</label>
                            </div>
                            <input class="form-control" type="date" name="expire_date" id="expDate" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="memberPostal" class="control-label col-md-3">Postal Code*</label>
                            <input type="number" name="postal_code" id="memberPostal" class="form-control" maxlength="256" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3">Gender*</label>
                            <div class="radio">
                                <label><input type="radio" name="gender" value="1" required> Male</label>
                                <label><input type="radio" name="gender" value="0" checked="" required> Female</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="memberAddress" class="control-label col-md-3">Address*</label>
                            <textarea name="member_address" id="memberAddress" class="form-control" rows="2" maxlength="30720" required></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="memberMailAddress" class="control-label col-md-3">Address Mail*</label>
                            <textarea name="member_mail_address" id="memberMailAddress" class="form-control" rows="2" maxlength="30720" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="memberPhone" class="control-label col-md-3">Phone Number*</label>
                            <input type="text" name="member_phone" id="memberPhone" class="form-control" maxlength="256" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="memberFax" class="control-label col-md-3">Fax Number*</label>
                            <input type="number" name="member_fax" id="memberFax" class="form-control" maxlength="256" required>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="memberNotes" class="control-label col-md-3">Notes:*</label>
                            <textarea name="member_notes" id="memberNotes" class="form-control" rows="2" maxlength="30720" required></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="image" class="control-label col-md-3">Foto*</label>
                            <input type="file" name="member_image" id="image" class="form-control" required>
                            <small>Maximum 500 KB</small>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="memberPasswd" class="control-label col-md-3">New Password*</label>
                            <input type="password" name="mPasswd" id="memberPasswd" class="form-control" onkeyup="validatePassword()" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="memberEmail" class="control-label col-md-3">E-mail*</label>
                            <input type="text" name="member_email" id="memberEmail" class="form-control" maxlength="256" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="memberPasswd2" class="control-label col-md-3">Confirmation New Password*</label>
                            <input type="password" name="memberPasswd2" id="memberPasswd2" class="form-control" onkeyup="validatePassword()" required>
                            <small id="passwordFeedback" style="color: red;"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3">Suspend Membership</label>
                            <div class="checkbox">
                                <label><input type="checkbox" name="is_pending[]" value="1"> Yes</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group text-right">
                    <div class="col-md-offset-3 ">
                        <input type="submit" class="btn btn-success text-right" value="Save">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>