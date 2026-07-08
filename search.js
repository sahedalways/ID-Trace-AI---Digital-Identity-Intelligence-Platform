/**
 * OSINT Universal Intelligence Console — Orchestration Logic
 * File: search.js
 */

window.logRotatorInterval = null;
window.activeTab = 'instagram';

// Centralized variation map for UI styles & labels (Updated targeting green accents)
const PLATFORM_CONFIGS = {
    instagram:  { label: "Instagram", accent: "hover:border-[#128c7e] text-[#128c7e] group-hover:text-[#128c7e]", domain: "www.instagram.com/xxx" },
    facebook:   { label: "Facebook",  accent: "hover:border-[#128c7e] text-[#128c7e] group-hover:text-[#128c7e]", domain: "www.facebook.com/xxx" },
    linkedin:   { label: "LinkedIn",  accent: "hover:border-[#128c7e] text-[#128c7e] group-hover:text-[#128c7e]", domain: "www.linkedin.com/in/xxx" },
    twitter:    { label: "Twitter",   accent: "hover:border-[#128c7e] text-[#128c7e] group-hover:text-[#128c7e]", domain: "x.com/xxx" },
    tiktok:     { label: "TikTok",    accent: "hover:border-[#128c7e] text-[#128c7e] group-hover:text-[#128c7e]", domain: "www.tiktok.com/@xxx" },
    truecaller: { label: "TrueCaller", accent: "hover:border-[#128c7e] text-[#128c7e] group-hover:text-[#128c7e]", domain: "" }
};

// Tactical, short operational phrases
const UNIVERSAL_LOGS = [
    { title: "Processing request..." },
    { title: "Querying database..." },
    { title: "Parsing handles..." },
    { title: "Extracting avatars..." },
    { title: "Filtering profiles..." },
    { title: "Verifying targets..." }
];

window.runLogRotation = function(networkName) {
    if (window.logRotatorInterval) clearInterval(window.logRotatorInterval);
    
    const liveTitle = document.getElementById('liveLoaderTitle');
    let index = 0;
    
    const updateUI = (log) => {
        if (liveTitle) liveTitle.textContent = log.title;
    };
    
    updateUI(UNIVERSAL_LOGS[0]);

    window.logRotatorInterval = setInterval(() => {
        index++;
        if (index >= UNIVERSAL_LOGS.length) {
            index = 0; 
        }
        updateUI(UNIVERSAL_LOGS[index]);
    }, 1800);
};

window.updateBrowserURL = function(tabName) {
    if (typeof activeSearchQuery === 'undefined' || !activeSearchQuery) return;
    window.history.pushState({}, '', `${window.location.pathname}?q=${encodeURIComponent(activeSearchQuery)}&engine=${tabName}`);
    const field = document.getElementById('formEngineField');
    if (field) field.value = tabName;
};

window.switchNetworkTab = function(tabName) {
    if (typeof activeSearchQuery === 'undefined' || !activeSearchQuery) return;
    window.activeTab = tabName;

    const sandbox = document.getElementById('tabContentSandbox');
    const loader = document.getElementById('globalLoader');
    if (!sandbox || !loader) return;
    
    loader.classList.add('hidden');
    sandbox.classList.remove('hidden');

    const pulse = document.getElementById('globalStatusPulse');
    const text = document.getElementById('globalStatusText');
    if (text) text.textContent = `Orchestrating ${tabName.charAt(0).toUpperCase() + tabName.slice(1)} Lookup Matrix...`;
    if (pulse) { pulse.className = "w-2.5 h-2.5 rounded-full bg-[#128c7e] animate-ping"; }

    window.updateBrowserURL(tabName);

    // INJECT INLINE LOADER ELEMENTS WITH STYLIZED STATIC SLEUTH EMOJI LAYER
    sandbox.innerHTML = `
        <div id="${tabName}Skeleton" class="max-w-md mx-auto text-center py-14 fade-in-up my-4">
            <div class="relative inline-flex items-center justify-center mb-6">
                <div class="w-20 h-20 rounded-full border-2 border-emerald-100 border-t-[#128c7e] animate-spin"></div>
                <div class="absolute text-3xl select-none pointer-events-none">
                    🕵️‍♂️
                </div>
            </div>
            <h4 class="text-sm font-bold text-black tracking-tight" id="liveLoaderTitle">Initializing...</h4>
        </div>
        <div id="${tabName}ResultsArea" class="space-y-6 hidden">
            <div id="${tabName}ResultsGrid" class="grid grid-cols-1 gap-3"></div>
            <div id="${tabName}DynamicFallbackBox" class="bg-gray-50 border border-gray-200/80 p-5 rounded-xl max-w-xl mx-auto text-center mt-6 fade-in-up shadow-sm"></div>
        </div>
    `;
    
    window.runLogRotation(tabName);
    window.executePlatformDataFetch(tabName, activeSearchQuery);
};

window.executePlatformDataFetch = function(platform, query) {
    const grid = document.getElementById(`${platform}ResultsGrid`);
    const resultsArea = document.getElementById(`${platform}ResultsArea`);
    const skeleton = document.getElementById(`${platform}Skeleton`);
    if (!grid || !skeleton || !resultsArea) return;

    const conf = PLATFORM_CONFIGS[platform] || { label: platform, accent: "hover:border-[#128c7e] text-[#128c7e]", domain: "example.com" };
    const classes = conf.accent.split(' ');

    fetch(`fetch_${platform}.php?action=ajax_scan&q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success' || !data.results?.length) {
                grid.innerHTML = window.getEmptyStateHTML(conf.label);
                document.getElementById(`${platform}DynamicFallbackBox`).classList.add('hidden');
            } else {
                grid.innerHTML = data.results.map(item => {
                    const isVerified = item.is_verified === true || item.is_verified === 'true' || String(item.is_verified).toLowerCase() === 'true';
                    
                    const badgeHtml = isVerified 
                        ? `<span class="inline-flex items-center text-blue-500 ml-1.5 flex-shrink-0" title="Verified Account">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px] fill-current" viewBox="0 0 24 24">
                                <path d="M23 12l-2.44-2.79.34-3.69-3.61-.82-1.89-3.2L12 2.96 8.6 1.5 6.71 4.7l-3.61.81.34 3.68L1 12l2.44 2.79-.34 3.69 3.61.82 1.89 3.2L12 21.04l3.4 1.46 1.89-3.2 3.61-.82-.34-3.69L23 12zm-13 5l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                            </svg>
                           </span>` 
                        : '';

                    const cleanName = (item.name || '').trim();
                    const firstLetter = cleanName ? cleanName.charAt(0).toUpperCase() : 'U';

                    const uiDisplayAvatar = item.avatar || item.raw_avatar || '';
                    const dbStorageAvatar = item.raw_avatar || item.avatar || '';

                    return `
                    <div class="search-result-row bg-white p-4 rounded-xl border border-gray-200/60 shadow-sm flex items-start justify-between gap-4 cursor-pointer ${classes[0]} transition-all duration-200 group w-full"
                         data-name="${window.escapeEngineHtml(item.name)}" 
                         data-avatar="${window.escapeEngineHtml(uiDisplayAvatar)}" 
                         data-raw-avatar="${window.escapeEngineHtml(dbStorageAvatar)}" 
                         data-source="${conf.label}" 
                         data-url="${window.escapeEngineHtml(item.link)}">
                        <div class="flex items-start gap-4 w-full min-w-0">
                            <div class="relative flex-shrink-0 rounded-full overflow-hidden border bg-gray-100 flex items-center justify-center" style="width: 64px; height: 64px;">
                                ${uiDisplayAvatar ? `
                                    <img src="proxy.php?url=${btoa(uiDisplayAvatar).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '')}" 
                                         class="result-avatar w-full h-full object-cover relative z-10" 
                                         onerror="
                                             this.style.display = 'none';
                                             this.nextElementSibling.classList.remove('hidden');
                                         "
                                    >
                                ` : ''}
                                <div class="letter-avatar w-full h-full flex items-center justify-center bg-emerald-50 text-[#128c7e] font-bold text-xl ${uiDisplayAvatar ? 'hidden' : ''}">
                                    ${firstLetter}
                                </div>
                            </div>
                            <div class="space-y-1 min-w-0 flex-grow text-left">
                                <h3 class="profile-name text-base font-semibold text-gray-800 transition-colors flex items-center gap-1.5 dynamic-wrap">
                                    <span>${window.escapeEngineHtml(item.name)}</span>${badgeHtml}
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed tracking-tight font-medium">
                                    ${item.handle}
                                </p>
                                ${item.biography ? `<p class="text-xs text-gray-400 break-words mt-1 leading-relaxed">${window.escapeEngineHtml(item.biography)}</p>` : ''}
                            </div>
                        </div>
                        <div class="action-icon-holder flex-shrink-0 text-gray-300 transition-colors pr-2 pt-1">
                            <i class="fa-solid fa-chevron-right text-base"></i>
                        </div>
                    </div>`;
                }).join('');

                // BUILD DYNAMIC FALLBACK SYSTEM IF PLATFORM IS NOT TRUECALLER
                const fallbackBox = document.getElementById(`${platform}DynamicFallbackBox`);
                if (platform !== 'truecaller' && fallbackBox) {
                    fallbackBox.innerHTML = `
                        <p class="text-xs sm:text-sm font-semibold text-black mb-2">
                            Didn't find your profile? Enter ${conf.label} profile link
                        </p>
                        <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl p-1 max-w-md mx-auto focus-within:border-[#128c7e] transition">
                            <input type="url" id="fallbackDirectUrlInput" placeholder="${conf.domain}" class="w-full bg-transparent text-xs sm:text-sm font-medium px-2.5 outline-none text-black h-9">
                            <button type="button" onclick="triggerDirectProfileLinkLookup(document.getElementById('fallbackDirectUrlInput').value)" class="bg-[#128c7e] hover:bg-[#0e6f64] text-white font-bold text-xs px-4 rounded-lg h-9 transition shadow-sm flex items-center justify-center cursor-pointer">
                                Analyze
                            </button>
                        </div>
                    `;
                    fallbackBox.classList.remove('hidden');
                } else if (fallbackBox) {
                    fallbackBox.classList.add('hidden');
                }
            }
            skeleton.classList.add('hidden');
            resultsArea.classList.remove('hidden');
            window.cleanupGlobalPulse(platform);
            
            if (typeof window.resetSearchButtonState === "function") {
                window.resetSearchButtonState();
            }
        })
        .catch(() => {
            window.handleFetchError(grid, skeleton);
            window.cleanupGlobalPulse(platform);
            
            if (typeof window.resetSearchButtonState === "function") {
                window.resetSearchButtonState();
            }
        });
};

window.getEmptyStateHTML = (name) => `
    <div class="bg-gray-50 rounded-2xl p-12 border border-gray-200 text-center max-w-md mx-auto my-4 w-full">
        <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3"><i class="fa-solid fa-users-slash text-xl"></i></div>
        <p class="text-sm text-black font-bold mb-1">No Profiles Discovered</p>
        <p class="text-xs text-gray-400">No public profiles matched that identifier inside ${name}'s scope.</p>
    </div>`;

window.handleFetchError = function(grid, skeleton) {
    if (window.logRotatorInterval) clearInterval(window.logRotatorInterval);
    if (skeleton) skeleton.classList.add('hidden');
    if (grid) {
        grid.classList.remove('hidden');
        grid.innerHTML = `<div class="bg-red-50 rounded-2xl p-6 border border-red-200 text-center max-w-lg mx-auto w-full"><i class="fa-solid fa-circle-exclamation text-2xl text-red-600 block mb-2"></i><p class="text-sm text-red-800 font-semibold">Error processing intelligence stream endpoints.</p></div>`;
    }
};

window.escapeEngineHtml = (str) => !str ? '' : String(str).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");

window.cleanupGlobalPulse = function(platform) {
    if (window.logRotatorInterval) clearInterval(window.logRotatorInterval);
    const text = document.getElementById('globalStatusText');
    const pulse = document.getElementById('globalStatusPulse');
    if (text) text.textContent = `${PLATFORM_CONFIGS[platform]?.label || platform} Target Profiles Synced`;
    if (pulse) { pulse.className = "w-2.5 h-2.5 rounded-full bg-emerald-500"; }
};

// Global Row Click Delegation — Cleaned & Opened on Own Tab
document.addEventListener('click', function (e) {
    const row = e.target.closest('.search-result-row');
    if (!row) return;
    e.preventDefault();

    const dbAvatarUrl = row.dataset.rawAvatar || row.dataset.avatar || '';

    fetch('create-view-session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            'name': row.dataset.name || row.querySelector('.profile-name')?.textContent.trim() || 'Unknown Identity',
            'avatar': dbAvatarUrl, 
            'source': row.dataset.source || window.activeTab, 
            'url': row.dataset.url || ''
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.vid) {
            window.location.href = `view.php?id=${encodeURIComponent(data.vid)}`;
        } else {
            throw new Error();
        }
    })
    .catch(() => {
        alert('Connection failure initializing workspace modules.');
    });
});
