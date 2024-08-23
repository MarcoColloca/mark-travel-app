<script>
import { store } from '../../../store';
import tt from '@tomtom-international/web-sdk-maps';

export default {
    props: {
        stages: Array
    },
    data() {
        return {
            store,
            resize: false,
            stagesCoordinates: [

            ],
            selectCoordinates: {
                lng: null,
                lat: null
            },
            myMap: {
                key: 'izOPNP8Trx6zf49WTM3IihGmIqnw2EnA',
                container: 'map',
                center: null,
                zoom: 10,
            },

        };
    },

    mounted() {
        this.setCoordinates();
        this.setMapCenter();

        this.initializeMap();

    },


    beforeDestroy() {
        if (this.map) {
            this.map.remove();
        }
    },

    methods: {
        setCoordinates() {
            this.stages.forEach(stage => {
                if (stage.lon && stage.lat) {
                    const newObj = {
                        lng: stage.lon,
                        lat: stage.lat
                    }

                    //console.log(newObj);

                    this.stagesCoordinates.push(newObj);
                }

            });
        },

        setMapCenter() {

            if (this.stagesCoordinates.length > 0 && this.stagesCoordinates[0].lat && this.stagesCoordinates[0].lng) {
                this.myMap.center = this.stagesCoordinates[0];
            }
            else {
                this.myMap.center = { lng: 7.682908, lat: 45.068688 } // Torino
            }
        },

        initializeMap() {
            const map = tt.map(this.myMap);

            if (this.stagesCoordinates.length > 0) {
                map.on('load', () => {
                    this.stagesCoordinates.forEach(el => {
                        new tt.Marker({ color: 'coral', pitchAlignment: 'auto' }).setLngLat(el).addTo(map)
                    });
                })
            }

            map.on('click', (event) => { this.getCoordinates(event) });
        },

        getCoordinates(event) {
            this.selectCoordinates.lng = event.lngLat.lng;
            this.selectCoordinates.lat = event.lngLat.lat;
        },

        addMarker() {
            let newObj = {}

            if (this.selectCoordinates.lat && this.selectCoordinates.lng) {
                newObj = { ...this.selectCoordinates }
            }

            this.stagesCoordinates.push(newObj)

            this.resetMap();
        },

        resetMap() {
            document.getElementById('map').innerHTML = '';
            this.initializeMap();

        },

        reduceDecimals(number, decimals = 9) 
        {
            const factor = Math.pow(10, decimals);
            return Math.floor(number * factor) / factor;
        },
        
    },

};
</script>



<template>
    <h5 class="btn btn-outline-danger reset" @click="resetMap()">Reset</h5>

    <div id="map"></div>


    <div class="my-border">
        <p>Longitudine: {{ reduceDecimals(selectCoordinates?.lng, 6) }}</p>
        <p>Latitudine: {{ reduceDecimals(selectCoordinates?.lat, 6) }}</p>
        <h5 class="btn btn-outline-coral" @click="addMarker()">Marker Temporaneo</h5>
    </div>
</template>






<style lang="scss" scoped>
p {
    color: white;
}

#map {
    height: 80%;
    width: 60%;
    margin-bottom: 10px;
}


.my-border {
    border: 2px solid coral;
    padding: 5px 10px;
}
</style>