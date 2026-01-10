<?php

return [
    "title" => "Simple, Transparent Pricing",
    "subtitle" => "Choose the plan that fits your restaurant",
    "monthly" => "Monthly",
    "annually" => "Annually",
    "save" => "Save 20%",
    "plans" => [
        "starter" => [
            "name" => "Starter",
            "price" => "$99",
            "period" => "/month",
            "description" => "Perfect for small restaurants",
            "features" => [
                "Up to 1,000 conversations/month",
                "Basic AI chatbot",
                "Email support",
                "2 integrations",
                "Basic analytics"
            ],
            "cta" => "Start Free Trial"
        ],
        "professional" => [
            "name" => "Professional",
            "price" => "$299",
            "period" => "/month",
            "description" => "For growing restaurant chains",
            "features" => [
                "Up to 10,000 conversations/month",
                "Advanced AI with learning",
                "Priority support",
                "Unlimited integrations",
                "Advanced analytics & reports",
                "Custom branding",
                "Multi-location support"
            ],
            "cta" => "Get Started",
            "popular" => true
        ],
        "enterprise" => [
            "name" => "Enterprise",
            "price" => "Custom",
            "period" => "",
            "description" => "For large restaurant groups",
            "features" => [
                "Unlimited conversations",
                "Dedicated AI training",
                "24/7 phone support",
                "Custom integrations",
                "White-label solution",
                "Dedicated account manager",
                "SLA guarantee"
            ],
            "cta" => "Contact Sales"
        ]
    ]
];
