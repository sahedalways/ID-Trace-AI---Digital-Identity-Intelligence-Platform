<?php
/**
 * OSINT Universal Intelligence Console — Target View Interface
 * File: view.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Master Review Databank Matrix (20 Domain-Specific Reviews)
$all_reviews = [
    [
        'name'       => 'Marcus Vance',
        'occupation' => 'Private Investigator',
        'country'    => 'United States',
        'content'    => 'An absolute game-changer for skip tracing. I managed to locate a childhood friend of a client who had been completely off the grid since 1998 using just an old email signature handle. They reunited last week.'
    ],
    [
        'name'       => 'Arif Rahman',
        'occupation' => 'Software Engineer',
        'country'    => 'Qatar',
        'content'    => 'I wanted to surprise my cousin for his birthday but had no idea what he was into lately. Ran a quick search profile, discovered his secret obsession with authentic wood-fired Neapolitan pizza, and sent a surprise gourmet voucher. He was completely blown away!'
    ],
    [
        'name'       => 'Sarah Jenkins',
        'occupation' => 'HR Manager',
        'country'    => 'United Kingdom',
        'content'    => 'We use this to verify applicant background footprints. Beyond strict professional profiles, it surfaced a candidate\'s old blog posts highlighting their true passion for digital automation—which was exactly the hidden trait our team needed.'
    ],
    [
        'name'       => 'Elena Rostova',
        'occupation' => 'Accountant',
        'country'    => 'Canada',
        'content'    => 'I spent three years trying to map out our scattered family tree branches across continents. Thanks to the deep profile match matrix here, I located a second cousin living just three blocks away from me in London!'
    ],
    [
        'name'       => 'David Kim',
        'occupation' => 'Business Owner',
        'country'    => 'South Korea',
        'content'    => 'A vendor was acting incredibly sketchy about their manufacturing capacity. Run their alias records through the console and immediately uncovered their cross-linked network connections. Saved my business over $15,000 in a bad contract.'
    ],
    [
        'name'       => 'Liam Johnston',
        'occupation' => 'Digital Marketer',
        'country'    => 'Australia',
        'content'    => 'Used the intelligence tools to map out a prospective high-ticket client\'s background. Found out he was heavily involved in local youth chess tournaments. Mentioned it naturally during our pitch meeting—closed the deal instantly.'
    ],
    [
        'name'       => 'Robert Chen',
        'occupation' => 'Data Analyst',
        'country'    => 'Singapore',
        'content'    => 'The absolute fastest way to aggregate public data footprints. I managed to clean up my own exposed legacy accounts and identity trails that I had completely forgotten existed over the last decade.'
    ],
    [
        'name'       => 'Amanda Ross',
        'occupation' => 'Real Estate Agent',
        'country'    => 'United States',
        'content'    => 'Before meeting anonymous out-of-state buyers at properties alone, I verify their safety footprint profiles here. It gives me and my agency total peace of mind knowing who we are actually dealing with in seconds.'
    ],
    [
        'name'       => 'Jamie Lindon',
        'occupation' => 'Graphic Designer',
        'country'    => 'United Kingdom',
        'content'    => 'I found a long-lost high school mentor who completely changed the trajectory of my career. I just wanted to say thank you to him after 15 years. This console mapped the correct social trail to reach him safely.'
    ],
    [
        'name'       => 'Michael Higgins',
        'occupation' => 'Consultant',
        'country'    => 'Ireland',
        'content'    => 'The cross-referencing capabilities on email addresses and alias nodes are unmatched. It cleanly maps data points that would normally take me hours of manual search strings to scrape together.'
    ],
    [
        'name'       => 'Chloe Dupuis',
        'occupation' => 'Journalist',
        'country'    => 'France',
        'content'    => 'Investigative source tracking requires total data accuracy. This tool let me quickly verify historical social footprints of a public figure, matching public records seamlessly to confirm an anonymous leak.'
    ],
    [
        'name'       => 'Stefan Weber',
        'occupation' => 'Project Manager',
        'country'    => 'Germany',
        'content'    => 'I was looking for an old military buddy I lost contact with over twelve years ago. Standard social platforms turned up nothing but dead ends. One deep search on here gave me a valid point of contact.'
    ],
    [
        'name'       => 'Carlos Mendez',
        'occupation' => 'Financial Analyst',
        'country'    => 'Mexico',
        'content'    => 'Extremely useful platform for performing basic digital diligence. It aggregated an old company digital registration signature that completely verified a new business group\'s legitimacy before we signed.'
    ],
    [
        'name'       => 'Yuki Tanaka',
        'occupation' => 'Web Developer',
        'country'    => 'Japan',
        'content'    => 'The search console pinpointed broken profile connections from an old open-source project I used to run. Was able to find my co-developer and pass over legacy code ownership smoothly.'
    ],
    [
        'name'       => 'Sophia Visser',
        'occupation' => 'Teacher',
        'country'    => 'Netherlands',
        'content'    => 'We discovered a collection of family diaries from the 90s mentioning a family contact in Canada. Using this platform, I successfully located his daughter and shared scanned copies of his handwriting. She cried!'
    ],
    [
        'name'       => 'Vikram Singh',
        'occupation' => 'Sales Executive',
        'country'    => 'India',
        'content'    => 'Phenomenal engine for verifying client background metadata points. Instead of cold calling completely blind, knowing a lead\'s public domain experience history completely shifts the dynamic of your initial pitch.'
    ],
    [
        'name'       => 'Oliver Hansen',
        'occupation' => 'Operations Manager',
        'country'    => 'Norway',
        'content'    => 'Someone left an expensive camera bag at our mountain lodge. Social channels yielded no matching handles, but running their forgotten loyalty card username through this system pulled a linked work address. Returned it safely.'
    ],
    [
        'name'       => 'Isabella Silva',
        'occupation' => 'Marketing Specialist',
        'country'    => 'Brazil',
        'content'    => 'I used this platform to research my grandmother’s line of relatives before traveling across the country. It map-pointed a verified cousin who welcomed us warmly and hosted our entire family stay!'
    ],
    [
        'name'       => 'Fahad Al-Mansoor',
        'occupation' => 'Network Engineer',
        'country'    => 'United Arab Emirates',
        'content'    => 'Outstanding execution on open-source entity cross-linking. I was checking out past credentials for a potential technical co-founder and verified their historic code community contributions flawlessly.'
    ],
    [
        'name'       => 'Emma Watson',
        'occupation' => 'Legal Assistant',
        'country'    => 'New Zealand',
        'content'    => 'Tracking down named heirs for estate distributions can turn into an absolute nightmare. This tool cut our research time in half by immediately generating associated address trees and contact channels.'
    ]
];

// 2. Dynamic Selection Layer (Shuffles and extracts exactly 4 items for grid compatibility within view.php)
shuffle($all_reviews);
$random_reviews = array_slice($all_reviews, 0, 4);