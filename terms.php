<?php
/**
 * File: terms.php
 * Terms and Conditions — exact content from Terms of Service.docx
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Terms of Service — Identity Search AI</title>
    <?php include 'head.php'; ?>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white bg-slate-50">

    <?php include 'navbar.php'; ?>

    <main class="flex-grow max-w-4xl w-full mx-auto px-4 sm:px-6 pt-12 pb-16">
        <div class="space-y-8">

            <div class="text-center space-y-2">
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Terms and Conditions</h1>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest pt-2">Last Updated: July 2026</p>
            </div>

            <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                <p class="text-xs sm:text-sm text-amber-900 font-semibold leading-relaxed">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 mr-1"></i>
                    WE ASK THAT YOU KINDLY CLOSELY REVIEW THESE TERMS AND CONDITIONS, INCLUDING THE DISPUTE RESOLUTION CLAUSES, OPTING-OUT LINK AND FCRA AND OTHER USE RESTRICTIONS SET FORTH BELOW) BEFORE YOU USE, ACCESS, POST OR PURCHASE ANY ITEM ON www.identitysearch.ai, THE Identity Search AI APPLICATION, OR ANY OTHER AFFILIATED WEBSITE OR MOBILE APPLICATION THAT LINKS TO AND UTILIZES THESE TERMS (ALL SUCH PLATFORMS, COLLECTIVELY REFERRED TO AS THE "SITE").
                </p>
            </div>

            <div class="space-y-4" id="termsAccordionContainer">

                <!-- 1 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">1. Introduction and an initial note regarding the Fair Credit Reporting Act and related obligations</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>These Terms and Conditions (collectively, the "Terms"), together with our Privacy Policy, represent the legally binding agreement between you ("You" or "Your") and Identity Search AI (our website) is provided by Cpabossaffiliate LLC , acting through Identity Search AI ('we', 'our' or 'us').</p>
                            <p>Identity Search AI is a database of publicly available sources of information aggregated for Your convenience, intended for personal individual use rather than for professional purposes, that makes available services that allow users to search for information as permitted by these Terms, such as to learn what information is in their own public records, to reconnect with long-lost friends or relatives, or, for example, to learn about the neighbors, online dates, acquaintances, buying/selling a personal item and other uses. Through Identity Search AI, end-user visitors to the Site can view and/or access certain content, information, media, text, data, images, graphics, user interfaces, audio, video, photographs, trademarks, logos, artwork, designs, magnetic translations, digital conversions, products, services, software functionality and other materials posted to or made available through the Site (collectively, "Content") as compiled, distributed and displayed by Identity Search AI and other third-party content providers ("Third-Party Providers") including, but not limited to, third-party websites or services that provide information about individuals (each, a "Search Subject") that can be searched for and accessed through the Site or other services We make available ("Identity Search AI Queries").</p>
                            <p class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm">WE DO NOT PROVIDE PRIVATE INVESTIGATOR SERVICES AND ARE NOT A CONSUMER REPORTING AGENCY AS DEFINED BY THE FAIR CREDIT REPORTING ACT 15 U.S.C. § 1681 et seq. ("FCRA") BECAUSE THE INFORMATION WE PROVIDE IS NOT COLLECTED OR PROVIDED, IN WHOLE OR IN PART, FOR THE PURPOSE OF SERVING AS A FACTOR IN ESTABLISHING A PERSON'S ELIGIBILITY FOR (a) CREDIT OR INSURANCE TO BE USED PRIMARILY FOR PERSONAL, FAMILY, OR HOUSEHOLD PURPOSES; (b) EMPLOYMENT PURPOSES; (c) BENEFITS, TENANCY OR EDUCATIONAL ADMISSION CONSIDERATIONS; OR (d) IN CONNECTION WITH A BUSINESS TRANSACTION INITIATED BY AN INDIVIDUAL CONSUMER FOR PERSONAL, FAMILY, OR HOUSEHOLD PURPOSES. WE DO NOT MAKE ANY REPRESENTATION OR WARRANTY AS TO THE CREDIT WORTHINESS, CREDIT STANDING, CREDIT CAPACITY, CHARACTER, GENERAL REPUTATION, PERSONAL CHARACTERISTICS, OR MODE OF LIVING OF ANY PERSON. AS SUCH, THE ADDITIONAL PROTECTIONS AFFORDED TO CONSUMERS AND OBLIGATIONS PLACED UPON CONSUMER REPORTING AGENCIES UNDER FCRA ARE NOT CONTEMPLATED BY, NOR CONTAINED WITHIN, THESE TERMS.</p>
                            <p>Accordingly, You acknowledge and agree that You will not conduct any Identity Search AI Queries or otherwise obtain or use any Content or other information obtained from or through the Site about a Search Subject or any person for purposes prohibited under FCRA. Because We are NOT a Consumer Reporting Agency, You are prohibited under FCRA from using any information obtained from the Site about a Search Subject including, but not limited to, information obtained through Identity Search AI Queries, as a factor in determining the Search Subject's eligibility for:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>Employment, including, but not limited to, to evaluate a Search Subject for initial employment, reassignment, promotion, or retention (including, but not limited to, household workers such as babysitters, cleaning personnel, nannies, contractors, and domestic workers);</li>
                                <li>Tenancy, including, but not limited to, deciding whether to lease a residential or commercial space to a Search Subject;</li>
                                <li>Educational Admission or Benefits, including, but not limited to, assessing a Search Subject's qualifications for an educational program or scholarship;</li>
                                <li>Personal Credit, Loans or Insurance, including, but not limited to, assessing the risk associated with providing credit, a loan or insurance based on a Search Subject's existing debt obligations; and/or</li>
                                <li>Business Transactions initiated by an individual consumer. Including, but not limited to, determining whether a Search Subject continues to meet the terms of a personal customer account.</li>
                            </ul>
                            <p>Nor may you use any Content in order to take any "adverse action" as such term is defined in FCRA. Using information about a Search Subject obtained from Us in any of the aforementioned ways violates both these Terms and the law and can lead to possible criminal penalties. We take this very seriously, and reserve the right to terminate user access, terminate Accounts, and report violators to law enforcement as appropriate. If You are not sure whether Your desired use of information obtained from Identity Search AI complies with these restrictions, please contact us at support@identitysearch.ai before conducting any Identity Search AI Queries or otherwise obtaining information about a Search Subject from Identity Search AI.</p>
                            <p>1.1. These rules lay down the standard terms and conditions for the utilization of the platform console located at identitysearch.ai and any background scanning automated outputs provided by Identity Search AI.</p>
                            <p>1.2. Services are managed, systematically deployed, and legally administered under international operational mandates by our registered business entity framework in the State of Wyoming, USA.</p>
                            <p>1.3. Users confirm explicit, uncompromised consent to these provisions and undertake to fully comply with them when initializing data tracking modules inside this portal in any way or form.</p>
                        </div>
                    </div>
                </div>

                <!-- 2 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">2. Scope of these Terms, License Grant, Electronic Signature and Legal Age Requirement</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>We reserve the right at any time and without notice to deny You access to the Site or to any portion thereof and to terminate Your rights under these Terms, in Our sole and absolute discretion. Your rights under these Terms will terminate automatically if You fail to comply with these Terms, subject to the survival rights of certain provisions identified herein. Termination will be effective without notice. Upon termination, You must promptly destroy all copies of any aspect of the Site in Your possession, custody or control.</p>
                            <p>These Terms govern: Your use and access of the Content We provide and/or make available; Your purchase of membership plans from the Site; and Your use of any Identity Search AI Queries provided to You in connection therewith. IDENTITY SEARCH AI GRANTS YOU A NON-EXCLUSIVE, NON-TRANSFERABLE (that means Your account is for You alone to use--Your neighbor, coworker, spouse or assistant should obtain their own accounts) REVOCABLE AND LIMITED LICENSE FOR INDIVIDUAL, PERSONAL (NOT PROFESSIONAL) USE AND PROVIDES THE USE OF THE SITE, THE RELATED CONTENT, AND THE IDENTITY SEARCH AI QUERIES (and, if You are a subscriber, certain other services) TO YOU ONLY ON THE CONDITION THAT YOU ACCEPT AND AGREE TO ALL OF THE TERMS CONTAINED HEREIN. You acknowledge and agree, however, that Identity Search AI may terminate this license at any time for any reason. BY USING THE SITE, YOU EXPRESSLY ACCEPT AND AGREE TO BE BOUND BY AND ABIDE BY ALL THE TERMS CONTAINED HEREIN, AND BY ACCEPTING THESE TERMS THROUGH THE COMPLETION OF A PURCHASE, SELECTION OF A METHOD OF PAYMENT, AND YOUR ENTRY OF PAYMENT METHOD INFORMATION, YOU HEREBY AUTHORIZE US TO CHARGE SUCH SELECTED PAYMENT METHOD AND ITS ASSOCIATED PAYMENT ACCOUNT THAT YOU HAVE SPECIFIED FOR THE PURCHASE OF ONE OF OUR MEMBERSHIP PLANS. IF YOU DO NOT AGREE WITH THE ENTIRETY OF THESE TERMS, YOU ARE NOT GRANTED PERMISSION TO AND MAY NOT ACCESS OR USE THIS SITE AND/OR THE CONTENT, AND YOU ARE HEREBY INSTRUCTED TO EXIT THE SITE IMMEDIATELY.</p>
                            <p>Accordingly, these Terms apply to You when You: (a) access, view, download, or otherwise use any page on the Site other than the home page located at www.identitysearch.ai; and/or (b) submit an online application to become a Identity Search AI user and/or subscriber, which enables You to utilize a host of services made available to such users/subscribers by and through the Site. By engaging in either of these actions, You acknowledge and agree that You (a) have read, understand and agree to be bound by these Terms in their entirety; (b) consent to the use of electronic signatures, contracts, orders and other records, and to the electronic delivery of notices, policies and records of transactions initiated or completed through the site or through any other interactions with Identity Search AI; and (c) waive any rights or requirements under any statutes, regulations, rules, ordinances or other laws in any jurisdiction which require (i) an original signature, (ii) delivery or retention of non-electronic records, or (iii) payments or the granting of credits in ways other than through electronic means. By providing Your email address, You agree to receive email from Us. The Site and its services are available only to individuals that are at least eighteen (18) years of age and that can enter into legally binding contracts under applicable law. If You are under eighteen (18) years of age or do not agree to these Terms in their entirety, do not access, view, download or otherwise use any page on the Site other than the home page located at www.identitysearch.ai and do not submit an online application to become a user or subscriber. The Identity Search AI Privacy Policy ("Privacy Policy") is part of these Terms and is incorporated herein by reference. By accepting these Terms, You hereby acknowledge, understand and agree to the collection and use of certain of Your personally identifiable information by the Site as described in the Privacy Policy. Any requests to remove Your information from Identity Search AI's People Search results will be governed by the procedures described in the Privacy Policy. Click here to view the Privacy Policy.</p>
                        </div>
                    </div>
                </div>

                <!-- 3 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">3. Class Action Waiver, Mandatory Arbitration, Dispute Resolution and Governing Law</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>BY USE OF THE SITE, YOU ACKNOWLEDGE AND AGREE TO OUR MANDATORY ARBITRATION OF DISPUTES PROVISION THAT GENERALLY REQUIRES THE USE OF ARBITRATION ON AN INDIVIDUAL BASIS TO RESOLVE DISPUTES, RATHER THAN JURY TRIALS OR CLASS ACTIONS, AND ALSO LIMITS THE REMEDIES AVAILABLE TO YOU IN THE EVENT OF A DISPUTE. You acknowledge and agree that all claims, disputes or controversies between You and Us (including against any of Our employees, agents, affiliates, subsidiaries, predecessors, successors or assigns) relating to the Site or related websites, the Content, Identity Search AI Queries, related services and materials, any related transaction or relationship and/or Your information, including, without limitation, tort and contract claims, claims based upon any federal, state or local statute, law, order, ordinance or regulation, the issue of arbitrability, shall be resolved by the final and binding arbitration procedures set below. You further acknowledge and agree that any such claims shall be brought solely in Your individual capacity and not as a plaintiff or class member in any purported class, representative proceeding, or private attorney general capacity. Similarly, you agree that any controversy concerning whether a dispute is arbitrable shall be determined by the arbitrator and not by a court and the arbitrator may not consolidate more than one person's claims and may not otherwise preside over any form of a representative or class proceeding. You voluntarily and knowingly waive any right to a jury trial.</p>
                            <p>BY AGREEING TO THIS AGREEMENT TO ARBITRATE, YOU ACKNOWLEDGE THAT YOU ARE GIVING UP YOUR RIGHT TO GO TO COURT AND YOUR RIGHT TO A JURY TRIAL. In arbitration, disputes are resolved by neutral arbitrators, rather than by a judge or jury. Arbitration is more informal than a court trial, however, an arbitrator can award relief.</p>
                            <p>Separate and apart from the agreement to arbitrate set forth above, You hereby waive any right to bring or participate in any class action in any way related to, or arising from, these Terms or the matters that they describe. You acknowledge that this class action waiver is material and essential to the arbitration of any dispute(s) You may have and is non-severable from this agreement to arbitrate claims.</p>
                            <p>YOU UNDERSTAND THAT BY AGREEING TO THIS AGREEMENT TO ARBITRATE, WHICH CONTAINS THIS CLASS ACTION LITIGATION AND CLASS ARBITRATION WAIVER, YOU MAY ONLY BRING CLAIMS AGAINST US, OUR AGENTS, OFFICERS, SHAREHOLDERS, MEMBERS, EMPLOYEES, SUBSIDIARIES, AFFILIATES, PREDECESSORS IN INTEREST, SUCCESSORS AND/OR ASSIGNS IN AN INDIVIDUAL CAPACITY AND NOT AS A PLAINTIFF OR CLASS MEMBER IN ANY PURPORTED CLASS ACTION OR REPRESENTATIVE PROCEEDING. IF YOU DO NOT AGREE TO THIS ARBITRATION AGREEMENT AND CLASS ACTION WAIVER, YOU MUST TELL US IN WRITING AND NOT USE OUR SITE, IDENTITY SEARCH AI QUERIES OR OUR SERVICES.</p>
                            <p>These Terms shall be treated as though they were executed and performed in Wyoming and shall be governed by and construed in accordance with the laws of the State of Wyoming (without regard to conflict of law principles).</p>
                            <p>At Identity Search AI, We expect that Our customer service team will be able to resolve most complaints You may have regarding Our provision, or Your use, of our Site and its services, such as Identity Search AI Queries. If You have a complaint, You can contact Our customer service team as described in the "How to Contact Us" section below. In the unlikely event that Your complaint remains unresolved, We prefer to specify now what each of us should expect in order to avoid any confusion later. Accordingly, You and Identity Search AI agree to the following resolution process for all disputes and claims that You or Identity Search AI may have arising from Our provision, or Your use, of our Site and its services, such as Identity Search AI Queries (each a "Service Claim").</p>
                            <p>In an attempt to find the quickest and most efficient resolution of any Service Claim, You and Identity Search AI agree to first discuss the Service Claim informally for at least 30 days. To do that, the party who brings the Service Claim must first send to the other party a notice that must include (1) a description of the Service Claim and (2) a proposed resolution. If You want to raise a Service Claim, You must send the aforementioned description and proposed resolution by email to support@identitysearch.ai. To subsequently discuss Your Service Claim with You, We will contact You using the email address you provided. If Identity Search AI wants to raise a Service Claim, We will send You the aforementioned description and claim notice to You at the email address that We have on file for You. If We do not have an email address for You on file, Identity Search AI will send Our Service Claim to You through a means that complies with the service of process rules of the State of Wyoming.</p>
                            <p>IF YOU AND IDENTITY SEARCH AI DO NOT REACH AN AGREED UPON RESOLUTION WITHIN 30 DAYS OF RECEIPT OF THE SERVICE CLAIM, YOU AND IDENTITY SEARCH AI AGREE THAT THE SERVICE CLAIM MUST BE RESOLVED THROUGH BINDING INDIVIDUAL (NOT CLASS) ARBITRATION WITH ARBITRATION RESOLUTION SERVICES INC. ("ARS") USING ITS RULES AND REGULATIONS GOVERNING THE SUBMISSION OF DISPUTES INVOLVING BUSINESSES AND INDIVIDUALS, AVAILABLE HERE. If ARS is unavailable or refuses to arbitrate the parties' dispute for any reason, the arbitration shall be administered and conducted by a widely-recognized arbitration organization that is mutually agreeable to the parties, if possible under any rules by such organization applicable to disputes between business and consumers, but neither party shall unreasonably withhold their consent.</p>
                            <p>EXCEPTIONS TO THIS ARBITRATION REQUIREMENT: EITHER PARTY HAS THE RIGHT TO PURSUE: AN INTELLECTUAL PROPERTY CLAIM OR CLAIM RELATING TO UNAUTHORIZED ACCESS TO DATA THROUGH THE SITE (INCLUDING, BUT NOT LIMITED TO, CLAIMS RELATING TO PATENT, COPYRIGHT, TRADEMARK, SERVICE MARK AND TRADE SECRETS AND CLAIMS RELATING TO THE ACCESS OR RETRIEVAL OF DATA THROUGH THE SITE USING AN AUTOMATED PROCESS SUCH AS SCRAPING) IN STATE OR FEDERAL COURTS LOCATED IN WYOMING. BOTH IDENTITY SEARCH AI AND YOU AGREE TO SUBMIT TO THE PERSONAL JURISDICTION OF THOSE COURTS FOR THESE CLAIMS.</p>
                            <p>NOTHING HEREIN SHALL BE CONSTRUED TO PRECLUDE ANY PARTY FROM SEEKING INJUNCTIVE RELIEF IN THE STATE OR FEDERAL COURTS LOCATED IN WYOMING IN ORDER TO PROTECT ITS RIGHTS PENDING AN OUTCOME IN ARBITRATION. TO HELP RESOLVE ANY ISSUES BETWEEN US PROMPTLY AND DIRECTLY, YOU AND IDENTITY SEARCH AI AGREE TO BEGIN ANY ARBITRATION OR COURT PROCEEDINGS ALLOWED UNDER THIS SECTION WITHIN ONE YEAR AFTER A CLAIM ARISES AND AGREE TO WAIVE THE RIGHT TO TRIAL BY JURY; OTHERWISE, THE CLAIM IS WAIVED.</p>
                            <p>Rather than force everyone to visit Us in Wyoming, ARS' arbitration contemplates arbitration without travelling anywhere, but instead via their cloud-based platform. Disagreements regarding the forum for arbitration will be settled by an ARS arbitrator.</p>
                            <p>When the 30-day period described above has elapsed, You may, as an individual (but not as a class) initiate the arbitration through the process described in ARS's Business and Individual Rules. If You initiate the arbitration, Your arbitration fees will be limited to the Application filing fee set forth by ARS rules. You and Identity Search AI acknowledge, understand and agree that any decision or award rendered by ARS may be entered in any court of competent jurisdiction. If the arbitrator rules against Identity Search AI, in addition to accepting whatever responsibility is ordered by the arbitrator, We think it fair that Identity Search AI reimburse Your reasonable attorneys' fees and costs, regardless of who initiated the arbitration. By contrast, if the arbitrator rules in Identity Search AI's favor, We will not seek reimbursement of Our attorneys' fees and costs, regardless of who initiated the arbitration.</p>
                            <p>This is a Class Action and Trial Waiver. IT IS IMPORTANT THAT YOU UNDERSTAND THAT BY ENTERING INTO THESE TERMS, YOU ARE WAIVING THE RIGHT TO PARTICIPATE IN A CLASS ACTION AGAINST US. THE ARBITRATOR'S DECISION OR AWARD WILL BE CONCLUSIVE AND BINDING AND MAY BE ENTERED AS A JUDGMENT IN ANY COURT OF COMPETENT JURISDICTION.</p>
                        </div>
                    </div>
                </div>

                <!-- 4 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">4. Definitions</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>Report / Dossier: Means a structured, aggregated compilation of open-source intelligence metrics providing public profile data footprints relative to a validated identity query target.</p>
                            <p>Platform Engine: The interactive UI configuration mechanisms, scanning infrastructure algorithms, database layers, and API network gateways accessible under the Identity Search AI brand parameters.</p>
                            <p>Private User: Any natural individual looking up infrastructure parameters for personal safety optimization, independent reputation tracking, or private validation objectives outside of primary commercial reselling channels.</p>
                        </div>
                    </div>
                </div>

                <!-- 5 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">5. User Workspace Accounts</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>5.1. To initialize digital footprint audits, trace data arrays, or persist historical intelligence records, operators must establish a workspace profile using an active electronic communication signature.</p>
                            <p>5.2. Workspace access codes and single-use verification links are strictly non-transferable. Operators assume direct responsibility for maintaining absolute confidentiality barriers around access tokens and account credentials.</p>
                            <p>5.3. Identity Search AI retains autonomous rights to limit access parameters, freeze analytical processing runs, or drop credentials instantly if behavior thresholds are breached or suspicious activity maps are registered.</p>
                        </div>
                    </div>
                </div>

                <!-- 6 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">6. Reporting & System Limitations</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>6.1. Identity Search AI acts solely as a specialized discovery overlay pipeline. We do not edit public registers, do not manage open data archives, and make no explicit claims regarding the absolute correctness or completeness of the open-source records returned.</p>
                            <p>6.2. Analytical calculations compile directly onto server environments. Dossier availability parameters vary based on system capacities, platform query limits, and target open-source availability levels.</p>
                            <p>6.3. Rendered dossiers remain persistently accessible within user profile storage sections for a fixed period of 30 days following generation, after which records are dropped automatically from storage layers.</p>
                        </div>
                    </div>
                </div>

                <!-- 7 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">7. Modifications to these Terms</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>We may modify these Terms, in whole or in part, from time to time in Our sole discretion, effective immediately upon posting modified Terms to the Site and, if You are a subscriber, by directly communicating them to You when You log in to the Site; provided, however, that: (i) any modification to the Dispute Resolution section shall not apply to any disputes initiated prior to the applicable modification; and (ii) any modification to the Membership Requirements and Conditions: Registration, Account Username and Password, Term/Termination and Fees, Taxes and Billing section shall not apply to any charges incurred prior to the applicable modification. By not terminating Your account ("Account") within seven (7) days after Our providing a notice of modifications to the Terms as described above or by continuing to use or access the Site or any of its services after modified Terms are posted to the Site, You agree to comply with, and be bound by, such modifications. Unless explicitly stated otherwise, any future offer(s) made available to You on the Site that augment(s) or otherwise enhance(s) the current features of the Site shall be subject to these Terms.</p>
                        </div>
                    </div>
                </div>

                <!-- 8 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">8. Content Moderation, Notice-and-Action, and Complaints</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p><b>I. Content Moderation and Restrictions</b></p>
                            <p>We may moderate, restrict, remove, disable access to, or otherwise take action with respect to any content, information, reviews, comments, or other material submitted to or made available on the Site ("User Content") where we determine, in good faith, that such content:</p>
                            <p>(a) violates these Terms or any applicable policies; (b) is unlawful or alleged to be unlawful under applicable law; (c) may cause harm to the Site, our users, or third parties; or (d) we are required to remove or restrict by law, court order, or competent authority.</p>
                            <p>Content moderation decisions may be made based on human review, automated tools, or a combination of both. Automated tools, where used, are applied to assist with the detection, identification, and prioritization of potentially unlawful or policy-violating content and are subject to human oversight.</p>
                            <p><b>II. Notice-and-Action Mechanism</b></p>
                            <p>We provide a mechanism that allows users and third parties to submit notices reporting content that they believe is unlawful or otherwise violates these Terms ("Notices"). Notices may be submitted through the content moderation or reporting features made available on the Site. Where a Notice includes sufficient information to allow us to identify the content and assess the claim, we will review it in a timely, diligent, and non-arbitrary manner and take appropriate action where warranted.</p>
                            <p><b>III. Statement of Reasons</b></p>
                            <p>Where we remove, restrict, disable access to, or otherwise take action against User Content, or suspend or terminate an account, based on a determination that the content is unlawful or violates these Terms, we will provide the affected user with a statement of reasons explaining:</p>
                            <p>(a) the action taken; (b) the grounds for the decision, including whether it was based on illegality or a violation of these Terms; and (c) the available means of redress.</p>
                            <p>We may withhold or limit such notice where providing it would expose us or others to legal liability, compromise the integrity or security of our services, interfere with investigations or enforcement efforts, be technically infeasible, or where we are prohibited from doing so by law.</p>
                            <p><b>IV. Internal Complaint-Handling Process</b></p>
                            <p>If we take action to remove, restrict, disable access to, or otherwise limit User Content, or suspend or terminate an account based on alleged illegality or violation of these Terms, the affected user may submit a complaint regarding that decision by:</p>
                            <p>(a) replying directly to the notice email containing the statement of reasons; or (b) sending an email to compliance@identitysearch.ai</p>
                            <p>Complaints must be submitted within six (6) months of the date of the decision. This complaint-handling process is electronic and free of charge. Complaints will be reviewed by appropriately trained personnel who were not solely responsible for the initial decision, where feasible. We will review complaints in a timely, diligent, non-arbitrary, and non-discriminatory manner and will communicate the outcome, including a statement of reasons, to the complainant.</p>
                            <p><b>V. Out-of-Court Dispute Resolution (EU Users)</b></p>
                            <p>Where applicable under the EU Digital Services Act, users may have the right to seek resolution of disputes relating to certain content moderation decisions through a certified out-of-court dispute settlement body, without prejudice to their right to pursue remedies through the courts.</p>
                            <p><b>VI. No Waiver of Other Rights</b></p>
                            <p>Nothing in this section limits our right to take immediate action where required to comply with legal obligations, prevent harm, or protect the integrity, security, or operation of the Site, nor does it affect any other rights or remedies available to us under these Terms or applicable law.</p>
                        </div>
                    </div>
                </div>

                <!-- 9 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">9. Some of Our Reserved Rights</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>We allow access to the Site as it may be available at any given time as contemplated and provided for in these Terms. You are solely responsible for Your use of the Site and any information You obtain from the Site. Identity Search AI reserves the right to:</p>
                            <p>A. monitor, view, record, review, audit or otherwise police Your or others' use of the Site, Identity Search AI Queries, Our other services or the associated information made available by subscribers during the application process ("Registration Data");</p>
                            <p>B. moderate any dispute between You and any other third party, including, but not limited to, disputes with other Site visitors, subscribers, or Search Subjects;</p>
                            <p>C. verify the identity of any person using the Site, including any user/visitor who applies to be a subscriber, as well as the purpose(s) for which any user/visitor or subscriber is using the Site;</p>
                            <p>D. not accept, reject or cancel any orders, or terminate any accounts, that We suspect, in Our sole discretion, to be fraudulent, whether the result of, or otherwise associated with fraudulent activity, or a violation of these Terms. We further reserve the right to cancel or not accept subsequent orders from customers with a previous fraudulent order history; and orders connected to previous credit card disputes; and/or</p>
                            <p>E. monitor the volume and uses of subscribers/users utilizing the Site for potentially professional purposes and audit, inquire as to, require reaffirmation of, restrict, limit or deactivate such accounts for compliance purposes and as We deem appropriate in Our sole discretion. Accordingly, please don't be offended if we ever need to contact You to better understand and ensure that any use is compliant with these Terms and applicable law. As a reminder, Users may not create multiple accounts with the purpose of accessing trials. Abusing the Site's trial allowance may result in suspension of accounts and service.</p>
                        </div>
                    </div>
                </div>

                <!-- 10 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">10. Proprietary Rights</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>The proprietary rights to all Content, including, without limitation, the Identity Search AI Queries, and any rights in the design, selection, arrangement, compilation, and coordination of such Content, are owned by or licensed to Identity Search AI and are protected under applicable laws (including, but not limited to, copyright, trademark and other intellectual property laws). Except as expressly provided in these Terms or with Identity Search AI's express written consent, You are not granted any rights or licenses to use any patents, copyrights, trade secrets, rights of publicity, trademarks, service marks, know-how or other proprietary rights of Identity Search AI or with respect to any of the Content. The "Identity Search AI®" name and logo as well as all custom graphics, icons and service names are trademarks of Identity Search AI and these and all other rights are reserved. All other trademarks are the property of their respective owners.</p>
                            <p>Identity Search AI reserves any and all rights not explicitly granted in these Terms. By using the Site, You do not acquire any ownership rights to the Site, Our services, Content, or any other information obtained therefrom.</p>
                        </div>
                    </div>
                </div>

                <!-- 11 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">11. Indemnification</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>You agree to defend, indemnify and hold Identity Search AI, its parents, subsidiaries, affiliates, joint ventures, and third-party service providers, and each of their respective members, officers, directors, employees, agents, shareholders, co-branders, content licensors, suppliers, contractors, attorneys, and other partners, harmless from and against any and all liabilities, claims, expenses (including reasonable attorneys' fees), damages, suits, costs, demands, and judgments made by any third party, including, but not limited to, by any Search Subject, arising from or related to: (a) Your use of the Site or any Content You obtain through the Site, including, but not limited to, information obtained through Identity Search AI Queries and other information about Search Subjects; (b) Your failure to comply with these Terms including, but not limited to, Your violation of any laws or any rights of another individual or entity; or (c) any claim that Identity Search AI is obligated to pay any taxes in connection with Your use of the Site, Our services or otherwise. The provisions of this paragraph are for the benefit of Identity Search AI, its parents, subsidiaries, affiliates, joint ventures, and third-party service providers and each of their respective officers, directors, members, employees, agents, shareholders, co-branders, licensors, suppliers, contractors, attorneys and other partners. Each of these individuals and entities shall have the right to assert and enforce these provisions directly against You on his, her, or its own behalf.</p>
                        </div>
                    </div>
                </div>

                <!-- 12 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">12. Limitation of Liability</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>YOU EXPRESSLY UNDERSTAND AND AGREE THAT IDENTITY SEARCH AI AND ITS PARENTS, SUBSIDIARIES, AFFILIATES, JOINT VENTURES AND THIRD-PARTY SERVICE PROVIDERS, AND EACH OF THEIR RESPECTIVE MEMBERS, OFFICERS, DIRECTORS, EMPLOYEES, AGENTS, SHAREHOLDERS, CO-BRANDERS, CONTENT LICENSORS, SUPPLIERS, CONTRACTORS, ATTORNEYS AND OTHER PARTNERS SHALL NOT BE LIABLE TO YOU OR ANY THIRD PARTY FOR ANY DIRECT, INDIRECT, PUNITIVE, INCIDENTAL, SPECIAL, CONSEQUENTIAL OR EXEMPLARY DAMAGES (though some states do not permit the exclusion or limitation of incidental or consequential damages, so the above limitation or exclusion may not apply to You) INCLUDING, BUT NOT LIMITED TO, DAMAGES FOR LOSS OF PROFITS, GOODWILL, USE, DATA OR INTANGIBLE LOSSES (EVEN IF IDENTITY SEARCH AI HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES), TO THE FULLEST EXTENT PERMITTED BY LAW, ARISING FROM OR RELATED TO: (a) THE USE OF OR THE INABILITY TO USE THE SITE, ANY INFORMATION CONTAINED THEREIN, THE CONTENT, IDENTITY SEARCH AI QUERIES OR ANY OTHER IDENTITY SEARCH AI PRODUCTS OR SERVICES; (b) THE COST OF PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES RESULTING FROM YOUR PURCHASE OF OR OBTAINING ANY IDENTITY SEARCH AI PRODUCTS, SERVICES, CONTENT OR OTHER DATA THROUGH THE SITE; (c) THE UNAUTHORIZED ACCESS TO, OR ALTERATION OF, YOUR REGISTRATION DATA OR ANY OTHER INFORMATION ABOUT YOU MAINTAINED BY IDENTITY SEARCH AI; AND (d) ANY OTHER DISPUTE RELATING TO THE SITE, ANY INFORMATION CONTAINED THEREIN, OR ANY OTHER IDENTITY SEARCH AI PRODUCTS OR SERVICES. THIS LIMITATION APPLIES TO ALL STATUTORY AND COMMON-LAW CAUSES OF ACTION INCLUDING, BUT NOT LIMITED TO, BREACH OF CONTRACT, BREACH OF WARRANTY, NEGLIGENCE, STRICT LIABILITY, MISREPRESENTATION AND ANY AND ALL OTHER TORTS. YOU HEREBY RELEASE IDENTITY SEARCH AI AND ITS PARENTS, SUBSIDIARIES, AFFILIATES, JOINT VENTURES AND THIRD-PARTY SERVICE PROVIDERS, AND EACH OF THEIR RESPECTIVE MEMBERS, OFFICERS, DIRECTORS, EMPLOYEES, AGENTS, SHAREHOLDERS, CO-BRANDERS, CONTENT LICENSORS, SUPPLIERS, CONTRACTORS, ATTORNEYS AND OTHER PARTNERS, FROM ANY AND ALL OBLIGATIONS, LIABILITIES, AND CLAIMS IN EXCESS OF THE LIMITATIONS STATED HEREIN. IF APPLICABLE LAW DOES NOT EXPLICITLY PROHIBIT SUCH LIMITATION, IN NO EVENT SHALL THE AGGREGATE MAXIMUM LIABILITY TO YOU UNDER ANY AND ALL CIRCUMSTANCES ARISING OUT OF OR RELATING TO THE USE OF THE SERVICES AND/OR THE CONTENT EXCEED THE GREATER OF ONE HUNDRED U.S. DOLLARS ($100.00) OR THE AMOUNT YOU PAY TO IDENTITY SEARCH AI, IF ANY, IN THE PAST SIX MONTHS, FOR ACCESS TO OR USE OF THE SERVICES. NO ACTION, REGARDLESS OF FORM, ARISING OUT OF YOUR USE OF THE SITE, ANY INFORMATION CONTAINED THEREIN, THE CONTENT, IDENTITY SEARCH AI QUERIES OR ANY OTHER IDENTITY SEARCH AI PRODUCT AND/OR SERVICE MAY BE BROUGHT BY YOU OR IDENTITY SEARCH AI MORE THAN ONE (1) YEAR FOLLOWING THE EVENT WHICH GAVE RISE TO THE CAUSE OF ACTION. THE LIMITATION OF LIABILITY SET FORTH IN THIS SECTION IS A FUNDAMENTAL ELEMENT OF THE BASIS OF THE BARGAIN BETWEEN YOU AND IDENTITY SEARCH AI AND ACCESS TO THE SITE WOULD NOT BE PROVIDED TO YOU WITHOUT SUCH LIMITATIONS. IN THE EVENT SOME JURISDICTIONS DO NOT ALLOW THE EXCLUSION OR LIMITATION OF DAMAGES TO THE EXTENT INDICATED ABOVE, OUR LIABILITY IN SUCH JURISDICTIONS SHALL BE LIMITED TO THE EXTENT PERMITTED BY LAW.</p>
                            <p>12.1. Under no structural or judicial criteria can Identity Search AI, its technical architects, or corporate entities be held liable for losses or complications resulting from user-side actions executed based on dossier analysis records.</p>
                            <p>12.2. This system compiles open OSINT variables and explicitly does not perform operations as a consumer reporting entity. All tracking results are completely barred from evaluation processes governed by Fair Credit Reporting Act (FCRA) provisions.</p>
                            <p>12.3. Governing legal jurisdiction parameters reside entirely under the statutory framework rules of the State of Wyoming, USA, without regard to conflict of law criteria.</p>
                        </div>
                    </div>
                </div>

                <!-- 13 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">13. Subscription, Billing Cancellation and Refund Policy</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>By selecting a membership plan (Monthly, Quarterly, Semi-Annual, or Annual) on Identity Search AI, you expressly authorize us to charge your payment method immediately for the initial term and automatically on a recurring basis at the start of each renewal period until you cancel.</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>Billing Cycles: Depending on the selected plan, your card will be charged every 30 days ($36/month), every 3 months ($55/quarter), every 6 months ($72/semi-annually), or every 12 months ($96/annually).</li>
                                <li>Billing Descriptor: Charges will appear on your bank or credit card statement as "IDENTITYSEARCH.AI".</li>
                                <li>Cancellation: You may cancel your subscription at any time to avoid future recurring billing through your account dashboard or by contacting support@identitysearch.ai at least 24 hours prior to your next renewal date.</li>
                                <li>30-Day Money-Back Guarantee: If you are not satisfied with our service, you are eligible to request a refund within 30 days of your initial purchase by emailing support@identitysearch.ai.</li>
                            </ul>
                            <p><b>How do I cancel my account?</b></p>
                            <p>Identity Search AI provides hassle-free online cancellation in many easy ways: For best results, login to your account. When logged in, visit the "Identity Search AI Account" page. Click "Subscription management" select "Cancel My Subscription." or You may also cancel by emailing support@identitysearch.ai, providing your Email Address and indicating your wish to cancel.</p>
                            <p><b>How to cancel app subscription?</b></p>
                            <p><b>Subscribed through Apple Store</b> - If you purchased your Identity Search AI subscription through the iOS app, your subscription is managed by Apple. Apple requires that you cancel your subscription through your Apple ID account. You can follow these steps to cancel your subscription: On your device, open the Settings app. Tap your name. Tap Subscriptions. Tap the active Identity Search AI subscription. Tap Cancel Subscription. You might need to scroll down to find the Cancel Subscription button. If there is no Cancel button or you see an expiration message in red text, the subscription is already canceled. For more information and screenshots of these steps, please see https://support.apple.com/en-us/118223. If you need assistance with this process, please contact Apple.</p>
                            <p><b>Steps to cancel (iPhone or iPad):</b> Open the Settings app. Tap your name. Tap Subscriptions. Tap the subscription that you want to manage. Tap Cancel Subscription. If you don't see Cancel, the subscription is already cancelled and won't renew.</p>
                            <p><b>Subscribed through Google Play</b> - The user can cancel the subscription by opening the Google Play app, tapping Account, then selecting Subscriptions and finally tapping the Cancel button. When a user cancels the subscription, the user will still have access to the product until the current paid period expires. Uninstalling the app will not automatically stop the subscription. The user must follow the described process to properly cancel the plan.</p>
                            <p><b>Steps to cancel (phone or tablet)</b> - On your Android device, go to your subscriptions in Google Play. Select the subscription you want to cancel. Tap Cancel subscription. Follow the instructions.</p>
                            <p><b>How can I get a refund?</b></p>
                            <p>If you are unhappy with our service or the data provided, you are covered by our 30-Day Money-Back Guarantee. You may request a full refund within 30 days of your initial plan purchase by emailing us at support@identitysearch.ai.</p>
                            <p>Please note the following general guidelines regarding our refund policy: Initial Purchases: Refund requests submitted within 30 days of the first transaction will be processed promptly. Recurring Renewals: Subsequent automatic renewal charges (monthly, quarterly, semi-annual, or annual) are non-refundable once billed, unless requested prior to the renewal date. Processing Time: Refunds are processed immediately on our end, but depending on your financial institution, it may take 5 to 10 business days for funds to reflect in your account. In-App Purchases: For charges made through the Apple App Store or Google Play, refunds must be requested directly through Apple or Google according to their respective policies.</p>
                            <p>Also please note the following general guidelines regarding our refund process: Refunds are processed immediately on our end, but depending on your bank or financial institution, it may take up to 10 days for the refund to post to your bank. Feel free to contact us if you have any questions or want to confirm your refund. For charges made through the Apple App Store or Google Play, you must request a refund through Apple or Google. Please see: Apple App Store Refunds or Google Play Refunds.</p>
                        </div>
                    </div>
                </div>

                <!-- 14 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">14. Treatment of Reversals and Chargebacks</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>As occurs with other online merchants, intentional credit/debit card reversals and chargebacks are frequently indicators of possible fraudulent use and/or theft of Our services and We may treat them as such. We reserve the right to investigate further and file complaints with the appropriate local and federal authorities. Please be advised that We regularly monitor all internet protocol address information and other user activity and that this information may be used in a civil and/or criminal case against any customer, especially in instances of possible theft or fraudulent behavior.</p>
                        </div>
                    </div>
                </div>

                <!-- 15 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">15. Entire Agreement</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>These Terms, the Privacy Policy, and all other applicable operating rules, policies, price schedules and other supplemental terms and conditions or documents that may be published or agreed upon by You from time to time, which are expressly incorporated herein by reference, shall constitute the entire and only agreement between You and Identity Search AI with respect to Your use of the Site. These Terms supersede all prior or contemporaneous agreements, representations, warranties and understandings with respect to Your use of the Site and the content contained therein. To the extent that any information or material that appears on or is posted to the Site, or otherwise is made available by Us, contains any representation, term or condition that is in conflict or inconsistent with these Terms, these Terms shall take precedence unless revised terms or conditions are contained in a signed writing by one of Our duly appointed officers.</p>
                        </div>
                    </div>
                </div>

                <!-- 16 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">16. Misconduct</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>Identity Search AI reserves the right to restrict, suspend or terminate Your Account or access to the Site if We determine, in Our sole and absolute discretion, that You have violated these Terms.</p>
                        </div>
                    </div>
                </div>

                <!-- 17 -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2sm">
                    <button type="button" class="w-full p-5 flex items-center justify-between text-left focus:outline-none terms-toggle-trigger group">
                        <span class="text-base font-black text-gray-900 group-hover:text-[#128c7e] transition-colors">17. Important Reminder: Certain Prohibited Uses of the Site</span>
                        <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-300 transform rotate-180"></i>
                    </button>
                    <div class="terms-content-slider transition-all duration-300 ease-in-out bg-white opacity-100">
                        <div class="px-5 pb-5 pt-1 text-sm sm:text-base text-black font-semibold leading-relaxed space-y-3">
                            <p>Operators explicitly commit to avoiding the following prohibited interaction behaviors:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>Deploying malicious automatic tools, scripts, or continuous automated scraping routines to read system database nodes.</li>
                                <li>Artificially bloating network processing metrics or dropping infrastructure latency via excessive multi-threaded API requests.</li>
                                <li>Using aggregated dossier outputs to engage in harassment, extortion, targeted identity fraud, or malicious exposure.</li>
                            </ul>
                            <p>As a reminder, We do NOT provide private investigator services or consumer reports and are NOT a consumer reporting agency--such terms have special meaning in the Fair Credit Reporting Act, 15 USC 1681 et seq., which are incorporated herein by reference. The information we provide is NOT collected or provided, in whole or in part, for the purpose of serving as a factor in establishing a person's eligibility for (a) credit or insurance to be used primarily for personal, family, or household purposes; (b) employment purposes; (c) benefits, tenancy or educational admission considerations; or (d) in connection with a business transaction initiated by an individual consumer for personal, family, or household purposes. We make NO representation or warranty as to the credit worthiness, credit standing, credit capacity, character, general reputation, personal characteristics, or mode of living of any person. The additional protections afforded to consumers and obligations placed upon consumer reporting agencies under FCRA are not contemplated by, nor contained within, these Terms.</p>
                            <p>You acknowledge and agree that You will not conduct any Identity Search AI Queries or otherwise obtain or use any Content or other information obtained from or through the Site about a Search Subject or any person for purposes prohibited under FCRA. Because We are NOT a Consumer Reporting Agency, You are prohibited under FCRA from using any information obtained from the Site about a Search Subject including, but not limited to, information obtained through Identity Search AI Queries, as a factor in determining the Search Subject's eligibility for:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>Employment, including, but not limited to, to evaluate a Search Subject for initial employment, reassignment, promotion, or retention (including, but not limited to, household workers such as babysitters, cleaning personnel, nannies, contractors, and domestic workers);</li>
                                <li>Tenancy, including, but not limited to, deciding whether to lease a residential or commercial space to a Search Subject;</li>
                                <li>Educational Admission or Benefits, including, but not limited to, assessing a Search Subject's qualifications for an educational program or scholarship;</li>
                                <li>Personal Credit, Loans or Insurance, including, but not limited to, assessing the risk associated with providing credit, a loan or insurance based on a Search Subject's existing debt obligations; and/or</li>
                                <li>Business Transactions initiated by an individual consumer. Including, but not limited to, determining whether a Search Subject continues to meet the terms of a personal customer account.</li>
                            </ul>
                            <p>Nor may you use any Content in order to take any "adverse action" as such term is defined in FCRA. Using information about a Search Subject obtained from Us in any of the aforementioned ways violates both these Terms and the law and can lead to possible criminal penalties. We take this very seriously, and reserve the right to terminate user access, terminate Accounts, and report violators to law enforcement as appropriate.</p>
                            <p>So, You may NEVER, under any circumstances, use Our Site, Content, Identity Search AI Queries or services or the information provided regarding Search Subjects to make decisions about employment, tenant screening, consumer credit, insurance or any other purpose that would require FCRA compliance or which is in violation of law (including, doxing, harassing or stalking someone). You agree that You will not use any information obtained from Our Site for purposes of and/or in connection with determining a prospective person or candidate's suitability for: employment, housing or accommodations, credit, health or any other insurance, loans, benefits, privileges or services provided by any business establishment, scholarships, tuition assistance, fellowships or education opportunities. The information We provide has NOT been collected in whole or in part for the purpose of furnishing consumer reports, as defined by FCRA. Accordingly, by using the Site, You recognize, acknowledge, understand and agree that You will not use any of the information obtained from the Site as a factor in: (a) evaluating an individual for employment, promotion, reassignment or retention (including employment of household workers such as babysitters, cleaning personnel, nannies, contractors, and other individuals); (b) establishing any individual's eligibility for personal credit, loans, insurance or assessing risks associated with existing consumer credit obligations; (c) evaluating an individual for educational opportunities, scholarships or fellowships; (d) evaluating an individual's eligibility for a license or other benefit granted by a government agency; (e) any effort to take any "adverse action" as such term is defined in FCRA; or (f) any other product, service or transaction in connection with which a consumer report is used under FCRA or any similar state statute, including, without limitation, job applications, check-cashing, apartment rentals or opening deposit or transaction accounts.</p>
                            <p>If You are not sure whether Your desired use of information obtained from Identity Search AI complies with these restrictions, please contact us at support@identitysearch.ai before conducting any Identity Search AI Queries or otherwise obtaining information about a Search Subject from Identity Search AI.</p>
                            <p>The information available on the Site is not necessarily 100% accurate, complete or up to date, so please do not use it in lieu of Your own common sense due diligence, especially where a person's criminal history may be concerned. We receive Our data from a variety of licensors and sources and cannot make any firm representation or warranty regarding the accuracy thereof or concerning the actual character or integrity of those about whom You inquire.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const container = document.getElementById("termsAccordionContainer");
        if (!container) return;
        container.querySelectorAll(".terms-content-slider").forEach(panel => {
            panel.style.maxHeight = panel.scrollHeight + "px";
        });
        container.addEventListener("click", (e) => {
            const trigger = e.target.closest(".terms-toggle-trigger");
            if (!trigger) return;
            const panel = trigger.parentElement.querySelector(".terms-content-slider");
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
