<?php
/**
 * File: privacy.php
 * Privacy and Cookies Policy — exact content from Privacy Policy.docx
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Privacy Policy — Identity Search AI</title>
    <?php include 'head.php'; ?>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white bg-slate-50">

    <?php include 'navbar.php'; ?>

    <main class="flex-grow max-w-4xl w-full mx-auto px-4 sm:px-6 pt-12 pb-16">
        <div class="space-y-8">

            <div class="text-center space-y-2">
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Privacy and Cookies Policy</h1>
                <p class="text-sm text-gray-500 font-medium leading-relaxed max-w-xl mx-auto">How we process operational dashboard data, secure workspace profiles, and treat open-source records.</p>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest pt-2">Last Updated: July 2026</p>
            </div>

            <div class="space-y-4" id="privacyAccordionContainer">

                <!-- 1 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">1. Introduction</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>1.1. IdentitySearch.ai (our website) is provided by CPABOSSAFFILIATE LLC, acting as Identity Search AI ('we', 'our' or 'us'). We are the controller of personal data obtained via our website, meaning we are the organization legally responsible for deciding how and for what purposes it is used. You can find out more about us in Section 16.</p>
                            <p>1.2. We are committed to safeguarding the privacy of our website visitors, service users, individual customers, customer personnel, individual contractors, consultants, and freelancers.</p>
                            <p>1.3. Our website incorporates privacy controls that allow you to manage the use of cookies and similar technologies. By adjusting these settings, you can choose which types of cookies are permitted (for example, performance or targeting cookies). You can access these controls at any time through the "Cookie Settings" button in the footer of our website.</p>
                            <p>1.4. We use cookies on our website. Insofar as those cookies are not strictly necessary for the provision of our website and services, we will ask you to consent to our use of cookies when you first visit our website.</p>
                        </div>
                    </div>
                </div>

                <!-- 2 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">2. Data Collection Frameworks</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>2.1. Account Metadata: When creating an account profile signature, we store your electronic mail address, submitted name configurations, and baseline geo-location values sent via network security layers.</p>
                            <p>2.2. Search Context Records: We temporarily log inbound lookups (e.g., target profile strings or full names) to systematically cycle automated profiling hooks across indexed public directories.</p>
                            <p>2.3. Payment Tokenization: Financial transactions are managed entirely by secure third-party billing providers. Our databases do not store raw credit card credentials or banking access indices.</p>
                            <p>2.4. We may process data enabling us to get in touch with you ("contact data"). The contact data may include your name, email address, telephone number, and postal address. The source of the contact data is you.</p>
                            <p>2.5. We may process your personal data in order to provide our data provision services ("service data"). The source of the service data is our third-party data providers, and it consists solely of information lawfully obtained from publicly available records and sources. This publicly available data may include:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>(a) Contact and Identity Information: Full name, previous names, aliases, addresses (current and historical), phone numbers, and email addresses.</li>
                                <li>(b) Relatives and Associates Information: Contact and identity information for possible relatives, associates, and neighbors.</li>
                                <li>(c) Social Media Data: Publicly available social media profiles, usernames, and identifiers.</li>
                                <li>(d) Other Public Information: Obituary records, unclaimed property information, and other legally accessible public data.</li>
                            </ul>
                            <p>2.6. We may process your personal data that are provided in the course of the use of our chatbot services and generated by our services in the course of such use ("chatbot data"). The chatbot data may include the personal data that you input into our chatbot. The source of the chatbot data is you and/or our services.</p>
                            <p>2.7. We may process information contained in or relating to any communication that you send to us or that we send to you ("communication data"). The communication data may include the communication content and metadata associated with the communication. Our website will generate the metadata associated with communications made using the website contact forms.</p>
                            <p>2.8. We may process data about your use of our website and services ("usage data"). The usage data may include your IP address, device ID, geographical location, browser type and version, operating system, referral source, length of visit, page views, and website navigation paths, as well as information about the timing, frequency, and pattern of your service use. The source of the usage data is our analytics tracking system, our advertising networks, and search information providers.</p>
                            <p>2.9. Please do not supply any other person's personal data to us, unless we prompt you to do so.</p>
                        </div>
                    </div>
                </div>

                <!-- 3 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">3. How We Utilize Collected Parameters</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>3.1. To initialize real-time open-source intelligence (OSINT) parsing mechanisms and compile coherent behavioral matrices for generated dossiers.</p>
                            <p>3.2. To authenticate dashboard logins using single-use security tokens (OTP) and securely deliver requested PDF data files or receipts straight to your inbox.</p>
                            <p>3.3. To safeguard the application console against excessive query flooding, scraping exploits, and automated profile mining attacks.</p>
                        </div>
                    </div>
                </div>

                <!-- 4 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">4. Purposes of Processing and Legal Bases</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>4.1. Identity Search AI acts as a search interface pipeline. The platform does not host, create, or maintain the underlying biographical entries returned inside our intelligence dossiers.</p>
                            <p>4.2. All analytical results are gathered dynamically on-demand from publicly searchable registers, social network tracks, metadata indexes, and open web directories.</p>
                            <p>4.3. Generated profiling history dossiers are automatically purged from our staging servers 30 days after creation to guarantee user query isolation.</p>
                            <p>4.4. Operations - We may process your personal data for the purposes of operating our website, the processing and fulfilment of orders, providing our services, generating invoices, bills and other payment-related documentation, and credit control. The legal basis for this processing is our legitimate interests, namely the proper administration of our website, services, suppliers and business.</p>
                            <p>4.5. Relationships and communications - We may process contact data, account data, transaction data and/or communication data for the purposes of managing our relationships, communicating with you (excluding communicating for the purposes of direct marketing) by email, SMS, mail, online chat and/or telephone, providing support services and complaint handling. The legal basis for this processing is our legitimate interests, namely communications with our website visitors, service users, individual customers and customer personnel, the maintenance of our relationships, enabling the use of our services and supplied services, and the proper administration of our website, services and business.</p>
                            <p>4.6. Marketing - We may process contact data, account data, customer relationship data, transaction data and/or usage data for the purposes of creating, targeting and sending direct marketing communications by email, making personalized suggestions and recommendations to you about our services that may be of interest to you, to deliver relevant website content and online advertisements to you and measure or understand the effectiveness of the advertising we serve to you. The legal basis for this processing is our legitimate interests (namely to carry out direct marketing, develop our services and grow our business to study how customers use our products/services, to develop them, to grow our business and to inform our marketing strategy) and (where we are required by law to obtain it) consent.</p>
                            <p>4.7. Record keeping - We may process your personal data for the purposes of creating and maintaining our databases, back-up copies of our databases and our business records generally. The legal basis for this processing is our legitimate interests, namely ensuring that we have access to all the information we need to properly and efficiently run our business in accordance with this policy.</p>
                            <p>4.8. Security - We may process your personal data for the purposes of security and the prevention of fraud and other criminal activity. The legal basis of this processing is our legitimate interests, namely the protection of our website, services and business, and the protection of others.</p>
                            <p>4.9. Insurance and risk management - We may process your personal data where necessary for the purposes of obtaining or maintaining insurance coverage, managing risks and/or obtaining professional advice. The legal basis for this processing is our legitimate interests, namely the proper protection of our business against risks.</p>
                            <p>4.10. Legal claims - We may process your personal data where necessary for the establishment, exercise or defense of legal claims, whether in court proceedings or in an administrative or out-of-court procedure. The legal basis for this processing is our legitimate interests, namely the protection and assertion of our legal rights, your legal rights and the legal rights of others.</p>
                            <p>4.11. Legal compliance and vital interests - We may also process your personal data where such processing is necessary for compliance with a legal obligation to which we are subject or in order to protect your vital interests or the vital interests of another natural person.</p>
                        </div>
                    </div>
                </div>

                <!-- 5 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">5. Data Sharing & Third-Party Protection</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>5.1. We do not sell, rent, lease, or lease-swap user dashboard logs or account lists to marketing networks, broker chains, or commercial advertising pools.</p>
                            <p>5.2. Operational metrics are only shared with verified system nodes (e.g., mail dispatch pathways, data routing providers, billing operators) strictly necessary to run the service interface.</p>
                            <p>5.3. We retain authority to disclose account variables exclusively if required to comply with binding court documentation, legal statutory requests, or active judicial processes.</p>
                        </div>
                    </div>
                </div>

                <!-- 6 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">6. Security & Infrastructure Protection</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>6.1. All incoming and outgoing data packages pass through high-tier Secure Socket Layer (SSL/TLS) encryption layers during active runtime processes.</p>
                            <p>6.2. Account authorization sequences leverage dynamic, single-use email verification tokens (OTP tokens) to eliminate risks linked to standard static password leaks or credential stuffing exploits.</p>
                            <p>6.3. While we enforce strict server monitoring protocols to isolate databases, no method of digital transmission over public routing channels can guarantee absolute, unbreachable protection metrics.</p>
                        </div>
                    </div>
                </div>

                <!-- 7 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">7. User Privacy Rights & Deletion Opt-Outs</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>7.1. Users maintain full authority to inspect, update, or completely erase their registered account signatures and historical trace structures from the active management panel.</p>
                            <p>7.2. If you want to request a manual deletion of your workspace profile or log history from all platform database nodes, you can file an explicit ticket request with our support desk at support@identitysearch.ai.</p>
                            <p>7.3. Once a profile signature removal request is confirmed, all associated user attributes are dropped immediately from our active production staging systems.</p>
                        </div>
                    </div>
                </div>

                <!-- 8 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">8. Retaining and Deleting Personal Data</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>8.1. This Section 8 sets out our data retention policies and procedures, which are designed to help ensure that we comply with our legal obligations in relation to the retention and deletion of personal data.</p>
                            <p>8.2. Personal data that we process for any purpose or purposes shall not be kept for longer than is necessary for that purpose or those purposes.</p>
                            <p>8.3. We will retain your personal data as follows:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>(a) Contact data (such as names, email addresses, phone numbers): retained for up to 2 years from last contact where used for marketing, and up to 10 years from last contact where held in customer relations records;</li>
                                <li>(b) Account data (such as login credentials, subscription information, and user account history): retained for up to 10 years from the date of account closure;</li>
                                <li>(c) Service data (data provided in connection with the performance of a contract): retained for up to 10 years from the end of the relevant contract;</li>
                                <li>(d) Chatbot data: retained for up to 10 years from the end of the relevant contract;</li>
                                <li>(e) Transaction data (such as payment records and expense records): retained for up to 10 years from the date of transaction. We do not retain full credit card numbers;</li>
                                <li>(f) Communication data (such as customer support requests, queries, and complaints): retained for up to 10 years from closure;</li>
                                <li>(g) Usage data (such as cookie/analytics data and system monitoring logs): generally retained for up to 2 years from collection, or up to 6 years in the case of security and system monitoring logs.</li>
                            </ul>
                            <p>8.4. Notwithstanding the other provisions of this Section 8, we may retain your personal data where such retention is necessary for compliance with a legal obligation to which we are subject, or in order to protect your vital interests or the vital interests of another natural person.</p>
                        </div>
                    </div>
                </div>

                <!-- 9 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">9. Security of Personal Data</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>9.1. We will take appropriate technical and organizational precautions to secure your personal data and to prevent the loss, misuse or alteration of your personal data.</p>
                            <p>9.2. We will store your personal data on secure servers, personal computers and mobile devices, and in secure manual record-keeping systems.</p>
                            <p>9.3. The following personal data will be stored by us in encrypted form: your name, contact information, and limited payment card details (such as the last four digits). We do not retain full credit card numbers.</p>
                            <p>9.4. Data relating to your enquiries and financial transactions that is sent from your web browser to our web server, or from our web server to your web browser, will be protected using encryption technology.</p>
                            <p>9.5. You acknowledge that the transmission of unencrypted (or inadequately encrypted) data over the internet is inherently insecure, and we cannot guarantee the security of data sent over the internet.</p>
                        </div>
                    </div>
                </div>

                <!-- 10 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">10. Your Rights</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>10.1. In this Section 10, we have listed the rights that you have under data protection law.</p>
                            <p>10.2. Your principal rights under data protection law are:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>(a) The right to access - you can ask for copies of your personal data;</li>
                                <li>(b) The right to rectification - you can ask us to rectify inaccurate personal data and to complete incomplete personal data. Please note that we pull service data straight from our third party data providers and any rectification requests will need to be actioned by them – we will provide you with contact details for this purpose;</li>
                                <li>(c) The right to erasure - you can ask us to erase your personal data;</li>
                                <li>(d) The right to restrict processing - you can ask us to restrict the processing of your personal data;</li>
                                <li>(e) The right to object to processing - you can object to the processing of your personal data;</li>
                                <li>(f) The right to data portability - you can ask that we transfer your personal data to another organization or to you;</li>
                                <li>(g) The right to complain to a supervisory authority - you can complain about our processing of your personal data; and</li>
                                <li>(h) The right to withdraw consent - to the extent that the legal basis of our processing of your personal data is consent, you can withdraw that consent.</li>
                            </ul>
                            <p>10.3. These rights are subject to certain limitations and exceptions. You can learn more about the rights of data subjects by visiting https://edpb.europa.eu/our-work-tools/general-guidance/gdpr-guidelines-recommendations-best-practices_en and https://ico.org.uk/for-organizations/guide-to-data-protection/guide-to-the-general-data-protection-regulation-gdpr/individual-rights/.</p>
                            <p>10.4. You may exercise any of your rights in relation to your personal data by written notice to us, using the contact details set out below.</p>
                        </div>
                    </div>
                </div>

                <!-- 11 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">11. Third Party Websites</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>11.1. Our website includes hyperlinks to, and details of, third party websites.</p>
                            <p>11.2. In general we have no control over, and are not responsible for, the privacy policies and practices of third parties.</p>
                        </div>
                    </div>
                </div>

                <!-- 12 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">12. Personal Data of Children</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>12.1. Our website and services are targeted at persons over the age of 18.</p>
                            <p>12.2. If we have reason to believe that we hold personal data of a person under that age in our databases, we will delete that personal data.</p>
                        </div>
                    </div>
                </div>

                <!-- 13 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">13. About Cookies</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>13.1. A cookie is a file containing an identifier (a string of letters and numbers) that is sent by a web server to a web browser and is stored by the browser. The identifier is then sent back to the server each time the browser requests a page from the server.</p>
                            <p>13.2. Cookies may be either "persistent" cookies or "session" cookies: a persistent cookie will be stored by a web browser and will remain valid until its set expiry date, unless deleted by the user before the expiry date; a session cookie, on the other hand, will expire at the end of the user session, when the web browser is closed.</p>
                            <p>13.3. Cookies may not contain any information that personally identifies a user, but personal data that we store about you may be linked to the information stored in and obtained from cookies.</p>
                        </div>
                    </div>
                </div>

                <!-- 14 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">14. Cookies That We Use</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>14.1. We use cookies for the following purposes:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>(a) Authentication and status - we use cookies to identify you when you visit our website and as you navigate our website, and to help us determine if you are logged into our website;</li>
                                <li>(b) Personalization - we use cookies to store information about your preferences and to personalize our website for you;</li>
                                <li>(c) Security - we use cookies as an element of the security measures used to protect user accounts, including preventing fraudulent use of login credentials, and to protect our website and services generally;</li>
                                <li>(d) Analysis - we use cookies to help us to analyze the use and performance of our website and services; and</li>
                                <li>(e) Cookie consent - we use cookies to store your preferences in relation to the use of cookies more generally.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 15 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">15. Cookies Used by Our Service Providers</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>15.1. Our service providers use cookies and those cookies may be stored on your computer when you visit our website. In particular, we use:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>(a) Analytics cookies - We use providers such as Google Analytics to help us understand how visitors use our website, improve performance, and create usage reports. Analytics cookies may track information such as pages visited, time spent on site, and referral sources.</li>
                                <li>(b) Advertising cookies - We work with advertising partners such as Google Ads/AdSense and Meta (Facebook, Instagram) to deliver relevant ads, measure ad performance, and provide personalized marketing.</li>
                                <li>(c) Social media cookies - Platforms such as Meta (Facebook, Instagram), LinkedIn, and Twitter/X may set cookies to enable integration of their services, personalize content, or measure engagement with our website.</li>
                                <li>(d) Security and fraud prevention cookies - Tools such as Google reCAPTCHA and Cloudflare may set cookies to help protect our website from abuse, detect fraudulent activity, and ensure system security.</li>
                                <li>(e) Payment service cookies - Payment processors may set cookies to enable secure transactions and prevent fraud.</li>
                                <li>(f) Customer support and communication cookies - Tools such as Zendesk or similar providers may use cookies to enable live chat, help desk services, or in-site messaging.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 16 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">16. Our Details</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>16.1. This website is owned and operated by CPABOSSAFFILIATE LLC.</p>
                            <p>16.2. You can contact us at: support@identitysearch.ai.</p>
                        </div>
                    </div>
                </div>

                <!-- 17 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">17. State-Specific Privacy Rights</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>17.1. State consumer privacy laws may provide residents with additional rights regarding our use of consumers' personal information. If you are a resident of California, your privacy rights are described in the Notice for California Residents section below.</p>
                            <p>17.2. If you are a resident of Colorado, Connecticut, Delaware, Iowa, Maryland, Minnesota, Montana, Nebraska, New Hampshire, New Jersey, Oregon, Tennessee, Texas, Utah or Virginia, you have the following rights:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>(a) Right of Access. You have the right to confirm whether we process your personal information and to access your personal information in a portable and easily usable format.</li>
                                <li>(b) Right to Deletion. You have the right to delete certain personal information.</li>
                                <li>(c) Right to Opt-out. You have the right to opt-out of the processing of personal information for purposes of sales/sharing, targeted advertising, or in furtherance of decisions that produce legal or similarly significant effects.</li>
                                <li>(d) Right to Correction. You have the right to request the correction of inaccuracies in certain personal information, taking into account the nature and the purposes of the processing of the personal information.</li>
                            </ul>
                            <p>17.3. You or your authorized agent may submit a request to exercise your opt-out, access, or deletion rights by emailing us at privacy@identitysearch.ai. Requests to correct inaccurate personal information that we maintain may also be sent to privacy@identitysearch.ai.</p>
                            <p>17.4. To update or correct inaccuracies in your personal information that was provided to us as part of creating or maintaining your account, you may do so by accessing your My Account page and selecting "Edit account info," or by contacting us at privacy@identitysearch.ai.</p>
                        </div>
                    </div>
                </div>

                <!-- 18 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none privacy-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">18. In Summary</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="privacy-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>18.1. At no cost, you may request information each year regarding any disclosure of your Public or Personal information to third parties for their own direct marketing purposes during the preceding calendar year. You have the right not to be discriminated against for exercising any of the rights listed above. To request access to or deletion of your information, or to exercise any other data rights under California law, please contact us using one of the methods set forth above.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const container = document.getElementById("privacyAccordionContainer");
        if (!container) return;
        container.querySelectorAll(".privacy-content-slider").forEach(panel => {
            panel.style.maxHeight = panel.scrollHeight + "px";
        });
        container.addEventListener("click", (e) => {
            const trigger = e.target.closest(".privacy-toggle-trigger");
            if (!trigger) return;
            const panel = trigger.parentElement.querySelector(".privacy-content-slider");
            const icon  = trigger.querySelector("i");
            if (panel.style.maxHeight === "0px" || panel.style.maxHeight === "") {
                panel.style.maxHeight = panel.scrollHeight + "px";
                panel.style.opacity = "1";
                icon.style.transform = "rotate(180deg)";
            } else {
                panel.style.maxHeight = "0px";
                panel.style.opacity = "0";
                icon.style.transform = "rotate(0deg)";
            }
        });
    });
    </script>
</body>
</html>