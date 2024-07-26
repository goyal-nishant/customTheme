<?php
/*
Template Name: Custom Map
*/
get_header(); 
?>

<div id="taxonomy-filter">
    <?php
    $terms = get_terms(array(
        'taxonomy' => 'location_category', 
        'hide_empty' => false,
    ));
    ?>

    <button data-term="all" class="taxonomy-button">All</button>
    <?php foreach ($terms as $term) : ?>
        <button data-term="<?php echo esc_attr($term->term_id); ?>" class="taxonomy-button">
            <?php echo esc_html($term->name); ?>
        </button>
    <?php endforeach; ?>
</div>

<div id="container">
    <div id="location-list">
        <?php echo get_location_buttons(); ?>
    </div>
    <div id="map"></div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA8ga5NZSYzrU2SnCqDssEB-kizBQzVipg&callback=initMap&v=weekly" defer></script>
<script>
    let map;
    let marker;

    function initMap() {
        const defaultCenter = { lat: 40.674, lng: -73.945 };

        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultCenter,
            zoom: 5,
            styles: [
                { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                { featureType: "administrative.locality", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
                { featureType: "poi", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
                { featureType: "poi.park", elementType: "geometry", stylers: [{ color: "#263c3f" }] },
                { featureType: "poi.park", elementType: "labels.text.fill", stylers: [{ color: "#6b9a76" }] },
                { featureType: "road", elementType: "geometry", stylers: [{ color: "#38414e" }] },
                { featureType: "road", elementType: "geometry.stroke", stylers: [{ color: "#212a37" }] },
                { featureType: "road", elementType: "labels.text.fill", stylers: [{ color: "#9ca5b3" }] },
                { featureType: "road.highway", elementType: "geometry", stylers: [{ color: "#746855" }] },
                { featureType: "road.highway", elementType: "geometry.stroke", stylers: [{ color: "#1f2835" }] },
                { featureType: "road.highway", elementType: "labels.text.fill", stylers: [{ color: "#f3d19c" }] },
                { featureType: "transit", elementType: "geometry", stylers: [{ color: "#2f3948" }] },
                { featureType: "transit.station", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
                { featureType: "water", elementType: "geometry", stylers: [{ color: "#17263c" }] },
                { featureType: "water", elementType: "labels.text.fill", stylers: [{ color: "#515c6d" }] },
                { featureType: "water", elementType: "labels.text.stroke", stylers: [{ color: "#17263c" }] }
            ]
        });

        marker = new google.maps.Marker({
            position: defaultCenter,
            map: map,
            icon: {
                url: 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png', 
                scaledSize: new google.maps.Size(40, 40) 
            }
        });

        const buttons = document.querySelectorAll('#location-list button');
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lng = parseFloat(this.getAttribute('data-lng'));
                const icon = this.getAttribute('data-icon');
                const newCenter = { lat, lng };

                map.setCenter(newCenter);

                marker.setPosition(newCenter);
                marker.setIcon({
                    url: icon,
                    scaledSize: new google.maps.Size(40, 40) 
                });
            });
        });
    }

    window.initMap = initMap;

    function resizeMap() {
    const mapElement = document.getElementById('map');
    mapElement.style.width = '100%';
    mapElement.style.height = '60vh'; 
}

window.addEventListener('resize', resizeMap); 
window.onload = () => {
    resizeMap();
    initMap(); 
};

</script>
<?php
get_footer(); 
?>
