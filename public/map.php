<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once 'includes/functions.php';

$lang = currentLang();
$pageTitle = __('explore_map');
require_once 'includes/seo.php';
renderMeta([
    'title' => $pageTitle . ' | Silk Road Explorer',
    'description' => __('map_explore_desc')
]);
require_once 'includes/layout_header.php';
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet MarkerCluster CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>

<style>
    #explore-map { height: calc(100vh - 80px); width: 100%; z-index: 1; }
    .map-filter-panel { position: absolute; top: 96px; left: 16px; z-index: 1000; }
    .leaflet-popup-content-wrapper { border-radius: 16px !important; box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important; padding: 0 !important; overflow: hidden; }
    .leaflet-popup-content { margin: 0 !important; min-width: 260px; }
    .leaflet-popup-tip { box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
    .marker-icon { display: flex; align-items: center; justify-content: center; border-radius: 50%; width: 40px; height: 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.25); border: 3px solid white; }
    .marker-place { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .marker-hotel { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .marker-restaurant { background: linear-gradient(135deg, #10b981, #059669); }
    .map-popup-img { width: 100%; height: 140px; object-fit: cover; }
    .map-popup-body { padding: 14px 16px; }
    .map-popup-title { font-weight: 700; font-size: 15px; color: #2a1f18; margin-bottom: 4px; line-height: 1.3; }
    .map-popup-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
    .map-popup-link { display: flex; align-items: center; gap: 6px; color: #d97706; font-weight: 600; font-size: 13px; text-decoration: none; margin-top: 10px; transition: color 0.2s; }
    .map-popup-link:hover { color: #b45309; }
    @media (max-width: 640px) { .map-filter-panel { left: 8px; right: 8px; } }
</style>

<!-- Filter Panel -->
<div class="map-filter-panel" x-data="{ open: true }">
    <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-silk-200/60 overflow-hidden max-w-xs">
        <button @click="open = !open" class="flex items-center justify-between w-full px-5 py-3.5 text-sm font-bold text-silk-800 hover:bg-silk-50 transition-colors">
            <span class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500 text-lg">filter_list</span>
                <?= __('map_filters') ?>
            </span>
            <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-transition class="px-5 pb-4 space-y-2 border-t border-silk-100">
            <label class="flex items-center gap-3 py-2 cursor-pointer group">
                <input type="checkbox" id="filter-places" checked onchange="toggleLayer('places')" class="w-4 h-4 rounded border-silk-300 text-amber-500 focus:ring-amber-500/30">
                <span class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-gradient-to-br from-amber-400 to-amber-600"></span>
                    <span class="text-sm font-medium text-silk-700 group-hover:text-silk-900"><?= __('nav_places') ?></span>
                </span>
            </label>
            <label class="flex items-center gap-3 py-2 cursor-pointer group">
                <input type="checkbox" id="filter-hotels" checked onchange="toggleLayer('hotels')" class="w-4 h-4 rounded border-silk-300 text-purple-500 focus:ring-purple-500/30">
                <span class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-gradient-to-br from-purple-400 to-purple-600"></span>
                    <span class="text-sm font-medium text-silk-700 group-hover:text-silk-900"><?= __('nav_hotels') ?></span>
                </span>
            </label>
            <label class="flex items-center gap-3 py-2 cursor-pointer group">
                <input type="checkbox" id="filter-restaurants" checked onchange="toggleLayer('restaurants')" class="w-4 h-4 rounded border-silk-300 text-emerald-500 focus:ring-emerald-500/30">
                <span class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600"></span>
                    <span class="text-sm font-medium text-silk-700 group-hover:text-silk-900"><?= __('nav_restaurants') ?></span>
                </span>
            </label>
            <div class="mt-4 pt-3 border-t border-silk-100">
                <button onclick="findMyLocation()" class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-xl font-semibold text-sm transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-base">my_location</span>
                    Yaqin atrofni topish
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Map Container -->
<div id="explore-map"></div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
const BASE = '<?= BASE_URL ?>';
const LANG = '<?= $lang ?>';

// Xarita yaratish — O'zbekiston markazida
const map = L.map('explore-map', {
    zoomControl: false
}).setView([41.3115, 69.2797], 6);

// Zoom control (o'ng tomonda)
L.control.zoom({ position: 'bottomright' }).addTo(map);

// Chiroyli tile layer
L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
    subdomains: 'abcd',
    maxZoom: 19
}).addTo(map);

// Marker icon yaratish
function createIcon(type) {
    const colors = { place: 'marker-place', hotel: 'marker-hotel', restaurant: 'marker-restaurant' };
    const icons  = { place: 'location_on', hotel: 'hotel', restaurant: 'restaurant' };
    return L.divIcon({
        className: '',
        html: `<div class="marker-icon ${colors[type]}"><span class="material-symbols-outlined" style="color:white;font-size:18px">${icons[type]}</span></div>`,
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -44]
    });
}

// Popup HTML yaratish
function popupHtml(m) {
    const name = m[`name_${LANG}`] || m.name_uz;
    const badges = { place: ['bg-amber-100 text-amber-700', 'Joy'], hotel: ['bg-purple-100 text-purple-700', 'Mehmonxona'], restaurant: ['bg-emerald-100 text-emerald-700', 'Restoran'] };
    const [badgeClass, badgeText] = badges[m.type] || badges.place;
    
    let extra = '';
    if (m.type === 'hotel' && m.stars) {
        extra = '<span class="text-amber-400 text-xs ml-2">' + '★'.repeat(m.stars) + '</span>';
    }
    if (m.type === 'restaurant' && m.cuisine) {
        extra = `<span class="text-silk-400 text-xs ml-2">· ${m.cuisine}</span>`;
    }
    
    const imgSrc = m.image 
        ? (m.image.startsWith('http') ? m.image : BASE + 'uploads/' + m.image) 
        : `https://placehold.co/400x200/2a1f18/ffc107?text=${encodeURIComponent(name.substring(0, 12))}`;
    
    return `
        <img src="${imgSrc}" alt="${name}" class="map-popup-img" onerror="this.src='https://placehold.co/400x200/2a1f18/ffc107?text=SRE'">
        <div class="map-popup-body">
            <span class="map-popup-badge ${badgeClass}">${badgeText}${extra}</span>
            <h3 class="map-popup-title" style="margin-top:8px">${name}</h3>
            <a href="${BASE}public/${m.url}" class="map-popup-link">
                <?= __('see_all') ?> <span class="material-symbols-outlined" style="font-size:16px">arrow_forward</span>
            </a>
        </div>
    `;
}

// Qatlamlar (layers)
const layers = {
    places: L.markerClusterGroup({ maxClusterRadius: 50 }),
    hotels: L.markerClusterGroup({ maxClusterRadius: 50 }),
    restaurants: L.markerClusterGroup({ maxClusterRadius: 50 })
};

// Markerlarni yuklash
fetch(BASE + 'public/api/markers.php')
    .then(r => r.json())
    .then(data => {
        data.forEach(m => {
            const marker = L.marker([m.lat, m.lng], { icon: createIcon(m.type) })
                .bindPopup(popupHtml(m), { maxWidth: 300, closeButton: true });
            
            const layerKey = m.type === 'place' ? 'places' : (m.type + 's');
            if (layers[layerKey]) {
                layers[layerKey].addLayer(marker);
            }
        });
        
        // Barcha qatlamlarni xaritaga qo'shish
        Object.values(layers).forEach(l => map.addLayer(l));
        
        // Agar markerlar bo'lsa, xaritani ularni ko'rsatadigan qilib sozlash
        const allMarkers = [...layers.places.getLayers(), ...layers.hotels.getLayers(), ...layers.restaurants.getLayers()];
        if (allMarkers.length > 0) {
            const group = L.featureGroup(allMarkers);
            map.fitBounds(group.getBounds().pad(0.1));
        }
    })
    .catch(e => console.error('Markers error:', e));

// Filtr toggle
function toggleLayer(type) {
    const checkbox = document.getElementById('filter-' + type);
    if (checkbox.checked) {
        map.addLayer(layers[type]);
    } else {
        map.removeLayer(layers[type]);
    }
}

// Joylashuvni aniqlash
let userMarker = null;
let userCircle = null;

function findMyLocation() {
    if (!navigator.geolocation) {
        alert("Brauzeringiz joylashuvni aniqlashni qo'llab-quvvatlamaydi.");
        return;
    }
    
    // Foydalanuvchiga qidirilayotganini bildiramiz
    const btn = document.querySelector('button[onclick="findMyLocation()"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="material-symbols-outlined text-base animate-spin">refresh</span> Qidirilmoqda...';
    btn.disabled = true;
    
    navigator.geolocation.getCurrentPosition((pos) => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        
        if (userMarker) {
            map.removeLayer(userMarker);
            map.removeLayer(userCircle);
        }
        
        userMarker = L.marker([lat, lng], {
            icon: L.divIcon({
                className: '',
                html: `<div style="background-color:#3b82f6; width:18px; height:18px; border-radius:50%; border:3px solid white; box-shadow:0 0 10px rgba(0,0,0,0.5);"></div>`,
                iconSize: [18, 18],
                iconAnchor: [9, 9]
            })
        }).bindPopup("<b style='font-size:14px'>Sizning joylashuvingiz</b><br><span style='color:#666;font-size:12px'>Sizga yaqin obidalar 10km radiusda</span>").addTo(map);
        
        userCircle = L.circle([lat, lng], { radius: 10000, color: '#3b82f6', fillColor: '#3b82f6', fillOpacity: 0.1, weight: 2 }).addTo(map);
        
        map.flyTo([lat, lng], 11, { animate: true, duration: 1.5 });
        
        // Qidiruv tugallandi
        btn.innerHTML = originalText;
        btn.disabled = false;
        
    }, (err) => {
        let msg = "Joylashuvni aniqlashda xatolik yuz berdi.";
        if(err.code === 1) msg = "Iltimos, joylashuvingizni aniqlashga ruxsat bering.";
        alert(msg);
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
    });
}
</script>

<?php require_once 'includes/layout_footer.php'; ?>
