document.addEventListener('alpine:init', () => {
    Alpine.data('bookingGeolocation', () => ({
        gettingLocation: false,
        locationError: null,
        getLocation() {
            this.gettingLocation = true;
            this.locationError = null;
            if (!navigator.geolocation) {
                this.locationError = 'Geolocalización no es soportada por tu navegador.';
                this.gettingLocation = false;
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.$wire.set('lat', position.coords.latitude);
                    this.$wire.set('lng', position.coords.longitude);
                    this.gettingLocation = false;
                },
                (error) => {
                    this.locationError = 'No se pudo obtener la ubicación. Por favor acepta los permisos.';
                    this.gettingLocation = false;
                }
            );
        }
    }));

    Alpine.data('bookingMap', (config) => ({
        lat: config.lat,
        lng: config.lng,
        map: null,
        marker: null,
        gettingLocation: false,
        initMap() {
            if (typeof google === 'undefined') {
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=${config.apiKey}&libraries=places`;
                script.async = true;
                script.defer = true;
                script.onload = () => this.setupMap();
                document.head.appendChild(script);
            } else {
                this.setupMap();
            }
        },
        setupMap() {
            const defaultLat = parseFloat(this.lat) || 4.6097;
            const defaultLng = parseFloat(this.lng) || -74.0817;
            const center = { lat: defaultLat, lng: defaultLng };

            this.map = new google.maps.Map(document.getElementById('map'), {
                center: center,
                zoom: 15,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
            });

            this.marker = new google.maps.Marker({
                position: center,
                map: this.map,
                draggable: true,
            });

            if (!this.lat || !this.lng) {
                this.updateCoords(defaultLat, defaultLng);
            }

            this.marker.addListener('dragend', () => {
                const pos = this.marker.getPosition();
                this.updateCoords(pos.lat(), pos.lng());
            });

            this.map.addListener('click', (e) => {
                const latLng = e.latLng;
                this.marker.setPosition(latLng);
                this.updateCoords(latLng.lat(), latLng.lng());
            });
        },
        updateCoords(lat, lng) {
            this.lat = lat;
            this.lng = lng;
            this.$wire.set('lat', lat);
            this.$wire.set('lng', lng);
        },
        getCurrentLocation() {
            this.gettingLocation = true;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        this.updateCoords(lat, lng);
                        if (this.map && this.marker) {
                            const newPos = new google.maps.LatLng(lat, lng);
                            this.map.setCenter(newPos);
                            this.marker.setPosition(newPos);
                        }
                        this.gettingLocation = false;
                    },
                    (error) => {
                        alert('No se pudo obtener la ubicación. Por favor acepta los permisos o mueve el pin manualmente.');
                        this.gettingLocation = false;
                    }
                );
            } else {
                this.gettingLocation = false;
            }
        }
    }));
});
