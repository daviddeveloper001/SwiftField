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
    }))
})
