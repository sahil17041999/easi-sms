<style>
    label {
        font-size: 13px;
    }

    .error {
        color: red;
        font-size: 11px;
    }

    .step-navigation {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 8px 0;
    }

    .step {
        text-align: center;
        flex: 1;
    }

    .step i {
        font-size: 24px;
        color: #555;
    }

    .step span {
        display: block;
        margin: 5px 0;
        font-size: 12px;
    }


    .step:not(:last-child)::after {
        content: '';
        width: 100%;
        height: 1px;
        background: #ddd;
        position: absolute;
        top: 50%;
        z-index: -1;
    }

    .step-line {
        flex-grow: 1;
        border: none;
        height: 2px;
        background-color: #ddd;
        margin: 0 10px;
    }

    li.ui-menu-item.ui-state-focus {
        color: #fff;
        background: #002866;
        border: 1px solid #002866;
    }


    .sms-textarea {
        border-radius: 6px;
        border: 1px solid #ccc;
        padding: 10px;
        font-size: 14px;
        resize: none;
        outline: none;
        transition: border-color 0.3s ease;
    }

    .number_input {
        border-radius: 6px;
    }

    #loader {
        display: none;
    }

    #loader.show {
        display: block;
    }

    .modal-title {
        font-size: 18px;
    }

    .modal-dialog {
        max-width: 500px;


    }

    .modal-content {
        border-radius: 6px;
        border: none;
    }

    #sms_box_model .modal-content {
        animation: fadeIn 0.3s ease-in-out;
    }

    .hidden {
        display: none;
    }

    .loader {
        --d: 22px;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        color: #4e73df;
        box-shadow:
            calc(1*var(--d)) calc(0*var(--d)) 0 0,
            calc(0.707*var(--d)) calc(0.707*var(--d)) 0 1px,
            calc(0*var(--d)) calc(1*var(--d)) 0 2px,
            calc(-0.707*var(--d)) calc(0.707*var(--d)) 0 3px,
            calc(-1*var(--d)) calc(0*var(--d)) 0 4px,
            calc(-0.707*var(--d)) calc(-0.707*var(--d))0 5px,
            calc(0*var(--d)) calc(-1*var(--d)) 0 6px;
        animation: l27 1s infinite steps(8);
    }

    @keyframes l27 {
        100% {
            transform: rotate(1turn)
        }
    }

    input.back_first_form.btn.border.btn-dark.btn-sm {
        width: 60px !important;
    }

    input.back_second_form.btn.border.btn-dark.btn-sm {
        width: 60px !important;

    }

    .mobile-preview-container {
        display: flex;
        justify-content: center;
        /* margin-top: 20px; */
    }

    .mobile-screen {
        width: 290px;
        height: 410px;
        border: 2px solid #ddd;
        border-radius: 0px 0px 10px 10px;
        background-color: #f9f9f9;
        position: relative;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .sms-preview {
        padding: 20px;
        font-size: 10px;
        color: #333;
        /* height: calc(100% - 40px); */
        overflow-y: auto;
        display: flex;
        align-items: center;
        /* justify-content: center; */
        text-align: center;
    }


    .top_hdng {
        background: #4e73df;
        border-radius: 10px 10px 0px 0px;
        padding: 8px 15px;
        /* //margin-top: 20px; */
    }

    span.user i.fas.fa-user {
        font-size: 15px;
        background: #fff;
        border-radius: 50px;
        width: 25px;
        height: 25px;
        text-align: center;
        line-height: 25px;
    }


    .left_icon {
        display: inline-block;
    }

    .right_icon {
        display: inline-block;
        float: right;
    }

    .phone i.fas.fa-phone-alt {
        color: #fff;
        font-size: 15px;
        transform: rotate(114deg);
        vertical-align: middle;
    }

    span.dots i.fas.fa-ellipsis-v {
        font-size: 15px;
        color: #fff;
        margin-left: 10px;
        vertical-align: middle;
    }

    .message-blue,
    p:before {
        content: '';
        position: absolute;
        width: 0;
        height: 0;
        border-top: 17px solid transparent;
        border-left: 16px solid transparent;
        border-right: 16px solid transparent;
        top: -1px;
        left: -17px;
    }

    .message-blue,
    p:after {
        content: '';
        position: absolute;
        width: 0;
        height: 0;
        border-top: 15px solid rgb(222, 224, 230);
        border-left: 15px solid transparent;
        border-right: 15px solid transparent;
        top: 0;
        left: -15px;
    }

    .message-blue {
        position: relative;
        margin-left: 20px;
        margin-bottom: 10px;
        padding: 10px;
        background-color:rgb(222, 224, 230);
        width: 200px;
        height: 100%;
        text-align: left;
        /* font: 400 .9em 'Open Sans', sans-serif; */
        border: 1px solid rgb(222, 224, 230);
        border-radius: 10px;
    }

    p#smsPreviewText {
        color: black;
        font-size: 11px;
    }
</style>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php define('BASE_PATH', dirname(__FILE__));
        include(BASE_PATH . '/../admin/includes/nav.php'); ?>
        <div class="container">
            <div class="row">
                <div class="mb-4 col-md-9">
                    <div class="card-header">
                        <div class="step-navigation">
                            <div class="step first-step">
                                <i class="first_icon fas fa-check-circle" style="color: #3073F1;"></i>
                                <span>Message</span>
                            </div>

                            <div class="step second-step">
                                <i class="second_icon fas fa-check-circle" style="color: #c8c4c4;"></i>
                                <span>Contact</span>
                            </div>

                            <div class="step third-step ">
                                <i class="third_icon fas fa-check-circle" style="color: #c8c4c4;"></i>
                                <span>Conformation</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body shadow-sm " style="padding: 52px;">
                        <form id="sms-form-data" role="form" action="javascript:void(0);" method="post">
                            <div class="box-body">
                                <div class="first_step">
                                    <div class="col-md-8 mx-auto">
                                        <div class="form-group">
                                            <label for="smsTemplate">Choose SMS Template</label>
                                            <select id="smsTemplate" class="form-control">
                                                <option value="">Select a SMS template</option>
                                                <?php foreach ($sms_template_list as $sms_key => $sms_template) {
                                                   if($sms_template['status'] == 1){
                                                ?>
                                                    <option value="<?= $sms_template['template_content'] ?>"><?= $sms_template['template_name'] ?></option>
                                                <?php } } ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8 mx-auto">
                                        <div class="form-group">
                                            <label for="smsMessage">Message text</label>
                                            <textarea id="smsMessage" name="message" class="form-control sms-textarea" placeholder="Type your SMS here..." rows="5"></textarea>
                                        </div>
                                        <input type="button" style="background-color: #4e73df;color:white" class="firststep_form btn btn-outline-primary btn-sm" value="Countine" />
                                    </div>
                                </div>

                                <div class="second_step hidden">
                                    <div class="col-md-8 mx-auto">
                                        <div class="form-group">
                                            <label for="smsType">Select SMS Type</label>
                                            <div class="mt-2">
                                                <input type="radio" id="singleSms" name="smsType" value="single" checked>
                                                <label for="singleSms">Single SMS</label>
                                                <input type="radio" id="bulkSms" name="smsType" value="bulk" class="mx-2">
                                                <label for="bulkSms">Bulk SMS</label>
                                            </div>
                                        </div>
                                        <div class="form-group single-sms-section">
                                            <label for="singleNumber">Sender</label>
                                            <input type="number" id="singleNumber" name="to[]" class="form-control smsNumber" placeholder="Enter Mobile Number">
                                        </div>
                                        <div class="form-group bulk-sms-section" style="display: none;">
                                            <label for="bulkContacts">Sender</label>
                                            <select id="bulkContacts" class="form-control " name="to[]">
                                                <option value="None" selected="selected">Select Number</option>
                                                <?php foreach ($sms_contacts_list as $___key => $_contacts_list) {
                                                    if ($_contacts_list['status'] == 1) {
                                                        $contactNumbers = json_decode($_contacts_list['contacts'], true);
                                                        if (!empty($contactNumbers) && is_array($contactNumbers)) {
                                                            $totalContacts = count($contactNumbers);
                                                            $total_numbers = implode(',', array_map(function ($contact) {
                                                                return htmlspecialchars($contact['contacts']);
                                                            }, $contactNumbers));
                                                ?>
                                                            <option value="<?= $total_numbers ?>"
                                                                data-channel="<?= $_contacts_list['channel_name'] ?>"
                                                                data-total="<?= $totalContacts ?>">
                                                                <?= $_contacts_list['channel_name'] ?>
                                                            </option>
                                                <?php
                                                        } else {
                                                            echo ' <option disabled>No contacts available</option>';
                                                        }
                                                    }
                                                } ?>
                                            </select>
                                            <input type="hidden" id="channelName" name="channel_name" value="Default Channel">
                                            <input type="hidden" id="mccCount" name="no_of_mcc" value="1">
                                        </div>
                                        <input type="button" class="back_first_form btn border btn-dark btn-sm px-2" value="Back" />
                                        <input type="button" style="background-color: #4e73df;color:white" class="secondstep_form btn btn-outline-primary btn-sm" value="Continue" />
                                    </div>
                                </div>


                                <div class="second_third hidden">
                                    <div class="col-md-8 mx-auto">
                                        <div class="form-group">
                                            <input type="button" class="back_second_form btn border btn btn-dark btn-sm" value="Back" />
                                            <button type="submit" style="background-color: #4e73df;color:white" class="btn border  btn-sm ">Send SMS</button>
                                            <div class="loader hidden" style="margin: 0 auto;margin-bottom:10px"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-3 first_step">
                <div class="first_step_in">
                    <button type="button" class="btn btn-default">gsdg</button>
                    <div class="top_hdng">
                        <div class="left_icon">
                            <span class="user">
                                <i class="fas fa-user"></i>
                                <span class="text-white">Easi SMS</span></span>
                        </div>
                        <div class="right_icon">
                            <span class="phone"><i class="fas fa-phone-alt"></i></span>
                            <span class="dots"><i class="fas fa-ellipsis-v"></i></span>
                        </div>
                    </div>
                    <div class="mobile-preview-container">
                        <div class="mobile-screen">
                            <div class="sms-preview">
                                <div class="message-blue">
                                    <p class="message-content" id="smsPreviewText">Your SMS preview will appear here.<br></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const singleSmsRadio = document.getElementById("singleSms");
            const bulkSmsRadio = document.getElementById("bulkSms");
            const singleSmsSection = document.querySelector(".single-sms-section");
            const bulkSmsSection = document.querySelector(".bulk-sms-section");

            function toggleSmsSection() {
                if (singleSmsRadio.checked) {
                    singleSmsSection.style.display = "block";
                    bulkSmsSection.style.display = "none";
                    document.getElementById("bulkContacts").disabled = true;
                    document.getElementById("singleNumber").disabled = false;
                } else if (bulkSmsRadio.checked) {
                    singleSmsSection.style.display = "none";
                    bulkSmsSection.style.display = "block";
                    document.getElementById("bulkContacts").disabled = false;
                    document.getElementById("singleNumber").disabled = true;
                }
            }
            singleSmsRadio.addEventListener("change", toggleSmsSection);
            bulkSmsRadio.addEventListener("change", toggleSmsSection);
            toggleSmsSection();
        });
    </script>


    <script>
        $(document).ready(function() {



            $("form").validate({
                rules: {
                    message: {
                        required: true
                    },
                    'to[]': {
                        required: true,
                        validateRecipientType: function() {
                            var selectedValue = $("select[name='to[]']").val(); // Grab the selected values from 'to[]'
                            return selectedValue !== null && selectedValue !== "None"; // Ensure it's not "None"
                        }
                        // validateRecipientType: function() {
                        //     var recipientType = $("#recipientType").val();
                        //     return recipientType !== "";
                        // }
                    }
                },
                messages: {
                    message: "Enter the message",
                    'to[]': "Please select a recipient",

                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                }
            });

            $.validator.addMethod("validateRecipientType", function(value, element) {
                var selectedValue = $(element).val();
                return selectedValue !== "None" && selectedValue.length > 0;
            }, "Please select a valid recipient.");



            $(".firststep_form").on("click", function() {
                if ($("form").valid()) {
                    $(".first_step").fadeOut(400, function() {
                        $(".second_step").fadeIn(400);
                        $(".first_icon").css("color", '#31b936');
                        $(".second_icon").css("color", '#3073F1');
                    });
                }
            });

            $(".secondstep_form").on("click", function() {
                if ($("form").valid()) {
                    $(".second_step").fadeOut(400, function() {
                        $(".second_third").fadeIn(400);
                        $(".second_icon").css("color", '#31b936');
                        $(".third_icon").css("color", '#3073F1');
                    });
                }
            });


            // Back button behavior
            $(".back_first_form").on("click", function() {
                $(".second_step").fadeOut(400, function() {
                    $(".first_step").fadeIn(400);
                    $(".first_icon").css("color", '#3073F1');
                    $(".second_icon").css("color", '#c8c4c4');
                });
            });

            $(".back_second_form").on("click", function() {
                $(".second_third").fadeOut(400, function() {
                    $(".second_step").fadeIn(400);
                    $(".second_icon").css("color", '#3073F1');
                    $(".third_icon").css("color", '#c8c4c4');
                });
            });

            $(".back_third_form").on("click", function() {
                $(".second_forth").fadeOut(400, function() {
                    $(".second_third").fadeIn(400);
                    $(".third_icon").css("color", '#3073F1');
                    $(".forth_icon").css("color", "#c8c4c4");
                });
            });

            $(".second_third button[type='submit']").on("click", function(event) {
                $('.loader').removeClass('hidden');
                event.preventDefault();
                if ($("form").valid()) {
                    var formData = new FormData($("form")[0]);
                    $.ajax({
                        url: '<?php echo base_url('easisms/send_sms'); ?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            const parsedData = JSON.parse(response);
                            console.log(parsedData.message);
                            const message = parsedData[0] && parsedData[0].message ? parsedData[0].message : '';
                            const statusArray = parsedData[0] && parsedData[0].response && parsedData[0].response.status ? parsedData[0].response.status : [];
                            const status = statusArray.length > 0 ? statusArray[0].status || '' : '';
                            // console.log(status);
                            // console.log(message);
                            if (status === '0 OK') {
                                $('.loader').addClass('hidden');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: message
                                }).then(() => {
                                    window.location.href = '<?php echo base_url("easisms/sms"); ?>';
                                });
                                $('#sms-form-data')[0].reset();
                            } else if (parsedData.status === 'error') {
                                $('.loader').addClass('hidden');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: parsedData.message
                                });
                            } else {
                                $('.loader').addClass('hidden');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const singleSmsSection = document.querySelector('.single-sms-section');
            const bulkSmsSection = document.querySelector('.bulk-sms-section');
            const smsTypeRadios = document.getElementsByName('smsType');
            const bulkContacts = document.getElementById('bulkContacts');
            const channelNameInput = document.getElementById('channelName');
            const mccCountInput = document.getElementById('mccCount');

            // Set default values for Single SMS
            channelNameInput.value = "Default Channel";
            mccCountInput.value = "1";

            // Toggle sections based on SMS type selection
            smsTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'single') {
                        singleSmsSection.style.display = 'block';
                        bulkSmsSection.style.display = 'none';
                        // Reset hidden inputs to default for single
                        channelNameInput.value = "Default Channel";
                        mccCountInput.value = "1";
                    } else {
                        singleSmsSection.style.display = 'none';
                        bulkSmsSection.style.display = 'block';
                    }
                });
            });

            // Update hidden fields based on bulk contacts dropdown selection
            bulkContacts.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const channelName = selectedOption.getAttribute('data-channel');
                const totalContacts = selectedOption.getAttribute('data-total');
                if (channelName && totalContacts) {
                    channelNameInput.value = channelName;
                    mccCountInput.value = totalContacts;
                } else {
                    channelNameInput.value = '';
                    mccCountInput.value = '';
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const smsTemplateDropdown = document.getElementById('smsTemplate');
            const smsMessageTextarea = document.getElementById('smsMessage');
            const smsPreviewText = document.getElementById('smsPreviewText');
            smsTemplateDropdown.addEventListener('change', function() {
                const selectedTemplate = this.value;
                smsMessageTextarea.value = selectedTemplate;
                updatePreview(selectedTemplate);
            });
            smsMessageTextarea.addEventListener('input', function() {
                const smsText = this.value;
                updatePreview(smsText);
            });

            function updatePreview(text) {
                smsPreviewText.textContent = text || 'Your SMS preview will appear here.';
            }
        });
    </script>