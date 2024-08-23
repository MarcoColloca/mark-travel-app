<script>
import { store } from '../../../store';
import Map from './Map.vue';

export default {
    components: {
        Map,
    },
    props: {
        day: Array
    },

    data() {
        return {
            store,
            loading: false,
            myDay: null,
            csrfToken: null,
            showMap: true,
            coverId: null,
            editStageRequest: {
                active: false,
                id: null,
                day_id: null,
                name: null,
                cover: null,
                description: null,
                curiosities: null,
                lat: null,
                lon: null,
                address: null,
                rating: null
            },
            deleteRequest: {
                active: false,
                id: null,
            },
            notes: {
                active: false,
                id: null,
            },
            deleteFormId: null,
        }
    },
    computed: {
        getStages() {
            return this.myDay.stages;
        }
    },
    beforeCreate() {
        //console.log(this.day)
    },

    mounted() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        this.getDay()
    },

    methods: {
        whileLoading() {
            if (this.loading === true) {
                return 'while-loading'
            }
            else {
                return ''
            }
        },

        getDay() {
            this.myDay = this.day[0];
        },

        // Metodi per Curiosità e Note
        displayCcuriosities(curiosities) {
            let curiositiesArray = curiosities.split('.').filter(Boolean).map(curiosity => curiosity + '.');

            return curiositiesArray
        },
        openNotes(id) {
            this.notes.id = id;
            this.notes.active = true;
        },
        colseNotes() {
            this.notes.id = null;
            this.notes.active = false;
        },

        // Metodi per la Modifica delle Tappe
        toggleEditStage(stage) {
            this.editStageRequest.img = stage.cover;
            this.editStageRequest.id = stage.id;
            this.editStageRequest.day_id = this.myDay.id;
            this.editStageRequest.name = stage.name;
            this.editStageRequest.cover = null;
            this.editStageRequest.curiosities = stage.curiosities;
            this.editStageRequest.description = stage.description;
            this.editStageRequest.notes = stage.notes;
            this.editStageRequest.lat = stage.lat;
            this.editStageRequest.lon = stage.lon;
            this.editStageRequest.rating = stage.rating;
            this.editStageRequest.address = stage.address;
            this.editStageRequest.active = !this.editStageRequest.active;
        },

        async updateStageForm(id) {
            this.loading = true;

            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('day_id', this.editStageRequest.day_id);
            formData.append('name', this.editStageRequest.name);
            if (this.editStageRequest.cover !== null) {
                formData.append('cover', this.editStageRequest.cover);
            }
            if (this.editStageRequest.curiosities !== null) {
                formData.append('curiosities', this.editStageRequest.curiosities);
            }
            if (this.editStageRequest.description !== null) {
                formData.append('description', this.editStageRequest.description);
            }
            if (this.editStageRequest.notes !== null) {
                formData.append('notes', this.editStageRequest.notes);
            }

            if (this.editStageRequest.address !== null) {
                formData.append('address', this.editStageRequest.address);
            }

            if(this.editStageRequest.lat !== null)
            {
                formData.append('lat', this.editStageRequest.lat);
            }

            if(this.editStageRequest.lon !== null)
            {
                formData.append('lon', this.editStageRequest.lon);
            }


            formData.append('rating', this.editStageRequest.rating);


            console.log(this.editStageRequest);

            await axios.post(`/admin/stages/${id}`, formData, {
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'multipart/form-data'
                }
            })

            this.store.forceReload();
        },


        handleFileChange(event) {
            this.editStageRequest.cover = event.target.files[0];
        },


        async addStageImg(event, id) {
            this.coverId = id;
            this.loading = true;
            const file = event.target.files[0];
            const formData = new FormData();
            formData.append('_method', 'PATCH')
            formData.append('id', id)
            formData.append('cover', file)
            await axios.post(`/admin/stages/addCover/${id}`, formData, {
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'multipart/form-data'
                }
            })

            this.store.forceReload();
        },


        hideGutterAndMargin(space) {
            if (!this.editStageRequest.active || !this.showMap) {
                return `${space}`
            }
            else if (this.editStageRequest.active || this.showMap) {
                return ''
            }
        },

        // Metodi per l'Eliminazione delle Tappe
        toggleDeleteStage(id) {
            this.deleteRequest.active = true;
            this.deleteRequest.id = id;
        },

        showDeleteRequest(id) {
            if (this.deleteRequest.active === true && this.deleteRequest.id === id) {
                return 'visible'
            }
            else {
                return ''
            }
        },
        hideDeleteRequest() {
            this.deleteRequest.active = false;
            this.deleteRequest.id = null;
        },

        async deleteStageForm(id) {
            this.loading = true;

            const formData = new FormData();
            formData.append('_method', 'DELETE');

            await axios.post(`/admin/stages/${id}`, formData, {
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                }
            })

            this.store.forceReload();
        },

    }
}
</script>

<template>
    <section v-if="myDay !== null" :class="hideGutterAndMargin('pb-5')">

        <!-- Form per la MODIFICA di un viaggio esistente -->
        <div class="add-stage-modal" v-if="editStageRequest.active" :class="whileLoading()">
            <div class="add-stage-window">
                <span class="btn link-danger close-stage-modal" @click="toggleEditStage">Annulla</span>
                <span class="w-100 d-flex justify-content-center">
                    <img :src="editStageRequest.img" class="card-img-top my-card-img" alt="Immagine Corrotta">
                </span>
                <form @submit.prevent="updateStageForm(editStageRequest.id)" :class="whileLoading()">
                    <!-- Nome -->
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="mt-5 form-label fw-bold" for="name">Nome del
                            viaggio:</label>
                        <input :class="whileLoading()" class="form-control" type="text" id="name" name="name"
                            v-model="editStageRequest.name" required>
                    </div>
                    <!-- Descrizione -->
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="description">Descrizione:</label>
                        <textarea :class="whileLoading()" class="form-control" id="description" name="description"
                            v-model="editStageRequest.description"></textarea>
                    </div>

                    <!-- Cover -->
                    <div class="mb-3">
                        <label for="image" :class="whileLoading()" class="form-label fb-bold">Carica un'immagine</label>
                        <input :class="whileLoading()" class="form-control text-blue" type="file" name="image" id="image"
                            accept=".jpg, .jpeg, .png, .bmp, .svg, .webp" @change="handleFileChange">
                    </div>

                    <!-- Note -->
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="notes">Note:</label>
                        <textarea :class="whileLoading()" class="form-control" id="notes" name="notes"
                            v-model="editStageRequest.notes"></textarea>
                    </div>

                    <!-- Curiosità -->
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="curiosities">Descrizione:</label>
                        <textarea :class="whileLoading()" class="form-control" id="curiosities" name="curiosities"
                            v-model="editStageRequest.curiosities"></textarea>
                    </div>

                    <!-- Indirizzo -->
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="address">Indirizzo:</label>
                        <input :class="whileLoading()" class="form-control" type="text" id="address" name="address"
                            v-model="editStageRequest.address">
                    </div>

                    <!-- Latitudine -->
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="lat">Latitudine: </label>
                        <input :class="whileLoading()" class="form-control" type="number" step="0.000001" id="lat" name="lat"
                            v-model="editStageRequest.lat">
                    </div>
                    <!-- Longitudine -->
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="lon">Longitudine:</label>
                        <input :class="whileLoading()" class="form-control" type="number" step="0.000001" id="lon" name="lon"
                            v-model="editStageRequest.lon">
                    </div>
                    <!-- Rating -->
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="rating">Valutazione del
                            viaggio:<small> (da 1 a 5)</small> </label>
                        <input :class="whileLoading()" class="form-control" type="number" id="rating" name="rating"
                            v-model="editStageRequest.rating" min="-1" max="5">
                    </div>

                    <div>
                        <button v-if="!loading" class="my-3 btn btn-outline-dark" type="submit">Modifica Tappa</button>
                        <p v-else class="text-center">
                            <span class="spin"></span>
                        </p>
                    </div>
                </form>
            </div>
        </div>



        <div class="text-center" :class="hideGutterAndMargin('my-5')">

            <h1><a :href="'/admin/days/showOne/' + myDay.trip.id">{{ myDay.trip.name }}</a></h1>
            <h2> Tappe del Giorno: {{ myDay.date }}</h2>
        </div>

        <div class="container">
            <div class="row" :class="hideGutterAndMargin('g-5')">
                <div class="col-12 col-md-6 col-lg-4" v-for="(stage, index) in myDay.stages" :key="stage.id">
                    <div class="card">
                        <!-- Immagine della Card -->
                        <div class="card-top my-card-top text-center">
                            <img class="card-img-top my-card-img" :src="stage.cover" alt="Immagine Corrotta"
                                v-if="stage.cover">
                            <div v-else>
                                <p v-if="loading && coverId === stage.id" class="text-warning">Caricamento immagine...
                                </p>
                                <label @click="console.log(stage.id)" v-else :for="'newStageImg' + stage.id"
                                    class="link link-coral p-1">Inserisci Immagine</label>
                                <input @change="(event) => { addStageImg(event, stage.id) }"
                                    :id="'newStageImg' + stage.id" class="text-center" type="file" hidden
                                    accept=".jpg, .jpeg, .png, .bmp, .svg, .webp"></input>
                            </div>
                        </div>

                        <!-- Info della Card -->
                        <div class="card-body my-day-card-body">
                            <!-- Link ai dettagli della Tappa -->
                            <a :href="'/admin/stages/' + stage.id">
                                <h4 class="card-title">{{ stage.name }}</h4>
                            </a>
                            <p><small>{{ stage.address }}</small></p>

                            <!-- Curiosità della Tappa -->
                            <div class="accordion my-accordion accordion-flush"
                                :id="'accordionFlushStageCuriosities' + index">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" :data-bs-target="'#flush-collapse' + index"
                                            aria-expanded="false" :aria-controls="'flush-collapse' + index">
                                            <strong>Curiosità:</strong>
                                        </button>
                                    </h2>
                                    <div :id="'flush-collapse' + index" class="accordion-collapse collapse"
                                        :data-bs-parent="'accordionFlushStageCuriosities' + index">
                                        <div class="accordion-body">
                                            <ul class="curiosities-list" :class="hideGutterAndMargin('mb-3')">
                                                <li v-for="curiosity in displayCcuriosities(stage.curiosities)">
                                                    <p>{{ curiosity ?? 'Nessuna Curiosità' }}</p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pulsati per Eliminazione e Modifica della Tappa -->
                            <div class="d-flex justify-content-around mt-5">
                                <p class="btn btn-outline-success" @click="toggleEditStage(stage)"> Modifica </p>
                                <form :class="whileLoading()">

                                    <div class="btn btn-link link-danger" @click="toggleDeleteStage(stage.id)">
                                        Elimina
                                    </div>

                                    <div :class="showDeleteRequest(stage.id)" class="my-modal">
                                        <h5 class="text-center">Vuoi Davvero Eliminare la Tappa?!</h5>
                                        <div class="my-modal__box" v-if="!loading">
                                            <p class="btn btn-danger my-modal-yes" @click="deleteStageForm(stage.id)">Sì
                                            </p>
                                            <p @click="hideDeleteRequest" class="btn btn-success my-modal-no">No</p>
                                        </div>
                                        <p v-else class="text-center">
                                            <span class="spin"></span>
                                        </p>
                                    </div>
                                </form>
                            </div>

                            <!-- Modale con le Note -->
                            <div class="my-stage-notes" v-if="notes.active && notes.id === stage.id">
                                <div class="d-flex justify-content-end">
                                    <p @click="colseNotes" class="my-stage-notes__exit">X</p>
                                </div>
                                {{ stage.notes ?? 'Nessuna Nota' }}
                            </div>
                        </div>

                        <!-- Voto e Note della Card -->
                        <div class="card-footer d-flex justify-content-between text-coral bg-dark">
                            <i class="bi bi-card-list" @click="openNotes(stage.id)"></i>
                            <span class="d-flex gap-1" v-if="stage.rating > 0">
                                <p v-for="star in stage.rating">&#9733;</p>
                            </span>
                            <span v-else>
                                <p>Nessun Voto.</p>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mappa delle Tappe -->
        <div class="text-center" :class="hideGutterAndMargin('mt-5')">
            <h3 @click="showMap = !showMap" class="btn btn-outline-coral">Mappa & Tappe</h3>
            <div v-show="showMap" class="map-container">
                <Map :stages="getStages"></Map>
            </div>
        </div>


    </section>
</template>

<style lang="scss" scoped>
// ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ Caricamento ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ 
.spin {
    display: inline-block;
    border: 8px solid rgba(255, 255, 255, 0.1);
    border-top: 8px solid coral;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}


@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.while-loading {
    pointer-events: none;
    color: rgba($color: #000000, $alpha: 0.4);
}

// ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ 

#rating {
    width: 80px;
}



.my-card-top {
    height: 300px;
    display: flex;
    flex-direction: column;
    justify-content: center;

    img {
        height: 300px;
    }
}

.my-day-card-body {
    min-height: 270px;
    position: relative;
}

.my-accordion {
    --bs-accordion-active-color: white;
    --bs-accordion-active-bg: rgba(255, 127, 80, 0.8);
    --bs-accordion-btn-focus-box-shadow: 0 0 0 0.25rem rgba(255, 127, 80, 0.25);
    --bs-accordion-btn-active-icon: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round'%3e%3cpath d='M2 5L8 11L14 5'/%3e%3c/svg%3e");
}


.curiosities-list {
    list-style-type: disc;
}


.bi-card-list {
    &:hover {
        cursor: pointer;
    }
}

/* Modali per la modifica di un viaggio */
.add-stage-modal {
    height: 100vh;
    width: 100vw;
    position: absolute;
    z-index: 99;
    background-color: rgba($color: #000000, $alpha: 0.7);

    .close-stage-modal {
        //position: absolute;
        display: flex;
        justify-content: end;
    }

    .add-stage-window {
        position: fixed;
        padding: 5px;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        background-color: white;
        color: black;
        overflow: scroll;
    }
}

@media(min-width: 500px) {
    .add-stage-modal {
        height: calc(100vh + 80px);
        width: 100vw;
        position: absolute;
        z-index: 99;
        background-color: rgba($color: #000000, $alpha: 0);

        .close-stage-modal {
            position: absolute;
            top: 5%;
            right: 5%;
        }

        .add-stage-window {
            min-width: 50%;
            min-height: 25%;
            position: absolute;
            top: 2%;
            left: 25%;
            bottom: auto;
            right: auto;
            background-color: white;
            color: black;
            padding: 20px 30px;
            border: 5px solid coral;
            border-radius: 18px;
        }
    }

}


.my-stage-notes {
    background-color: #212529;
    color: white;
    padding: 10px;
    border-radius: 8px;
    position: absolute;
    top: 5%;
    bottom: 5%;
    left: 0;
    right: 0;
    margin: 0 15px;

    .my-stage-notes__exit {
        text-align: right;
        align-self: end;
        max-width: fit-content;
        padding: 0 10px;
        color: rgb(182, 70, 70);
        border: 1px solid rgb(182, 70, 70);
        border-radius: 4px;

        &:hover {
            background-color: white;
            cursor: pointer;
        }
    }
}

.map-container {
    background-color: #212529;
    display: flex;
    padding: 50px 0;
    height: 800px;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
</style>