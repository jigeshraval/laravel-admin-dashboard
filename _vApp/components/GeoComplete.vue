<template>
    <div>
        <vuetify-google-autocomplete
            :id="id"
            append-icon="mdi-map-marker"
            :disabled="false"
            :placeholder="placeholder"
            :label="label"
            v-model="location"
            v-on:placechanged="getAddressData"
            :country="country"
        >
        </vuetify-google-autocomplete>

        <input type="hidden" name="street1" v-if="street1" :value="street1">
        <input type="hidden" name="city" v-if="city" :value="city">
        <input type="hidden" name="state" v-if="state" :value="state">
        <input type="hidden" name="zip" v-if="zip" :value="zip">
        <input type="hidden" name="lat" v-if="lat" :value="lat">
        <input type="hidden" name="lng" v-if="lng" :value="lng">
    </div>
</template>

<script>
import Vue from 'vue'
import VuetifyGoogleAutocomplete from 'vuetify-google-autocomplete'

Vue.use(VuetifyGoogleAutocomplete, {
  apiKey: 'AIzaSyBavGIZu3CL6ghTgGxdku8Q1mh7wOQ9nY8'
});

export default {
    props: {
        obj: {},
        id: {
            default: 'map'
        },
        placeholder: {
            default: 'Start typing'
        },
        label: {
            default: 'Location'
        },
        name: {
            default: 'location'
        },
        value: {
            default: function () {
                return [];
            }
        }
    },
    data () {
        return {
            street1: null,
            city: null,
            state: null,
            zip: null,
            lat: null,
            lng: null,
            lng: null,
            country: ['us'],
            location: ''
        }
    },
    watch: {
        'value' : function (val) {
            if (val) {
                console.log(val);
                this.location = val.location;
                this.street1 = val.street;
                this.city = val.city;
                this.state = val.state;
                this.zip = val.zip;
                this.lat = val.lat;
                this.lng = val.lng;
            }
        }
    },
    methods: {
        getAddressData (addressData, placeResultData, id) {
            if (addressData) {

                // this.location = placeResultData.formatted_address;
                this.street1 = addressData.name;
                this.city = addressData.locality;
                this.state = addressData.administrative_area_level_1;
                this.zip = addressData.postal_code;
                this.lat = addressData.latitude;
                this.lng = addressData.longitude;
                addressData.location = this.location;
                this.$emit('geo', addressData);

            }
        }
    }
}
</script>
