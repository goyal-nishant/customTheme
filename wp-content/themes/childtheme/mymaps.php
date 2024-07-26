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
            styles: [ /* map styles omitted for brevity */ ]
        });

        marker = new google.maps.Marker({
            position: defaultCenter,
            map: map,
            icon: {
                url: 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png', 
                scaledSize: new google.maps.Size(40, 40) 
            }
        });

        // Event listeners for location buttons
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

        // Event listeners for taxonomy filter buttons
        const taxonomyButtons = document.querySelectorAll('#taxonomy-filter .taxonomy-button');
        taxonomyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const termId = this.getAttribute('data-term');
                fetchLocations(termId);
            });
        });
    }

    function fetchLocations(termId) {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=filter_locations&term_id=' + termId)
            .then(response => response.text())
            .then(data => {
                document.getElementById('location-list').innerHTML = data;
                // Re-add event listeners for new location buttons
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
