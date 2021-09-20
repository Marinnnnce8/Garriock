<?php

namespace ProcessWire;

    if ($config->ajax) {
        
        // validate email
        $pageName = $page->template->name;
        if (false == $sanitizer->email($input->post->$pageName['Email'])) {
            $nb->outputJSON([
                    "action" => "Invalid Email",
                    "response" => 401,
                    "message" => "Please ensure your email address is valid.",
                ]);
        }
        
        if (isset($captcha)) {

            if ($captcha->verifyResponse() !== true) {
                $nb->outputJSON([
                    "action" => "reCAPTCHA",
                    "response" => 401,
                    "message" => "Please ensure the reCAPTCHA is checked.",
                ]);
            }
        }

        $subject = "$page->title Form Submission from $nb->siteUrl";
        $message = $nb->formEmail([
            "subject" => $subject,
            "prepend" => "<p>This is a response sent using the contact form on your website:</p>",
        ]);
        
        // find the division email
        $divisionId = (int) $input->post[$pageName]['division'] ?: false;
        
        if (!$divisionId) { // something has gone wrong, abort!
            die('something has gone wrong.');
        }
        $division = $pages->get($divisionId);
        
        $nb->outputJSON(
                $nb->sendEmail(
                    $subject, $message, [
                        "replyTo" => true,
                        'to' => $division->email,
                    ]
                )
        );
    }


/**
 * Contact Form
 *
 */
function buildForm($pages, $nb, $modules, $division) {

    // find the services related to the location
    $branch = $pages->findOne([
       'template'  => 'branch', 
       'id'  => $division->branch, 
    ]);
    
    $services = $pages->find([
                                    'template' => 'service',
                                    'id' => $branch->services,
                                ]);
    $servicesItems = ['na' => 'Select a service'];
    foreach ($services as $service) {
        $servicesItems[$service->title] = $service->title;
    }
    
    $captcha = $modules->isInstalled("MarkupGoogleRecaptcha") ? $modules->get("MarkupGoogleRecaptcha") : null;

    $wrapHalf = "<div class='uk-width-1-2@s'>";

    $form = $nb->buildForm(
            [
        'division' => [
            'attr' => [
                'type' => 'hidden',
                'value' => $division->id,
            ]
        ],        
        "Name" => [
            "attr" => [
                "class" => [
                    "uk-input",
                ],
                "title" => "Please enter your name",
                "placeholder" => "Type your full name*",
                "required" => true,
            ],
            "label" => "Your Name",
            "options" => [
                //"append" => "$wrapHalf</div>",
                "wrap" => $wrapHalf,
            ],
        ],
        "Company name" => [
            "attr" => [
                "type" => "text",
                "class" => [
                    "uk-input",
                ],
                "placeholder" => "Type your company name",
            ],
            "label" => [
                "text" => "Company name",
            ],
            "options" => [
                "wrap" => $wrapHalf,
            ],
        ],
        "Email" => [
            "attr" => [
                "type" => "email",
                "class" => [
                    "uk-input",
                ],
                "title" => "Please enter a valid email address",
                "placeholder" => "Type your email address*",
                "required" => true,
            ],
            "label" => [
                "text" => "Email",
            ],
            "options" => [
                "wrap" => $wrapHalf,
            ],
        ],
        "Type of Service" => [
            'input' => 'select',
            "attr" => [
                'class' => ['uk-input'],
            ],
            'label' => [
                'text' => 'Type of service'
            ],
            'options' => [
                'wrap' => $wrapHalf,
            ],
            'items' => $servicesItems,
        ],     
        "Message" => [
            "input" => "textarea",
            "attr" => [
                "class" => [
                    "uk-textarea",
                ],
                "title" => "Please enter your message",
                "placeholder" => "Type your message here*",
                "required" => true,
                "rows" => 9,
            ],
            "label" => [
                "text" => "Your Enquiry",
            ],
            "options" => [
                "wrap" => "<div class='uk-width-1-1'>",
            ],
        ],
        "submit" => [
            "input" => "button",
            "attr" => [
                "type" => "submit",
                "class" => [
                    "uk-button",
                    "uk-button-primary",
                ],
                "id" => 'submit-' . $division->id,
            ],
            "value" => "Send",
            "options" => [
                "prepend" => (isset($captcha) ? $captcha->render() . $captcha->getScript() : ""),
                "wrap" => "<div class='uk-width-1-1'>",
            ],
        ],
            ], 
        [
            "class" => [
                "uk-form-stacked",
                "uk-margin-top",
                "uk-margin-bottom",
            ],
            'name' => 'contact-form-' . $division->id,
            'id' => 'contact-form-' . $division->id,
            'data-division-id' => $division->id,
            'data-nb-form' => [
                            "msg" => [
                                "success" => "Thank you, your message has been sent to <strong>$division->email</strong>. We will be in touch soon.",
                                "danger" => "Sorry, the message could not be sent. Please refresh the page to try again.",
                            ],
                            "loading" => "Sending",
                        ]
        ], [
            "wrapInputs" => "<div class='uk-grid-small' data-uk-grid>",
            "label" => [
                "class" => "uk-form-label",
            ],
            //"name" => 'contact',
        ]
    );

    return $form;
}
