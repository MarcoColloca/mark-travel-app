<script>
import { store } from '../../../store';

export default {
    props:{
        stage: Array,
        images: Array,
    },
    
    data(){
        return{
            store,
            currentImgIndex: 0,
            csrfToken: null,
            myStage: null,
            myImages: null,
            loading: false,
            imagesContainer:{
                visible: false
            }
        }
    },

    computed:{
        dayId()
        {
            return this.myStage.day.id;
        },
        dayDate()
        {
            return this.myStage.day.date;
        },
        tripId()
        {
            return this.myStage.day.trip.id
        },
        tripName()
        {
            return this.myStage.day.trip.name
        },
        getImgUrl()
        {   
            let index = this.currentImgIndex;                  
            return this?.myImages[index]?.url
        }
    },

    mounted(){
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        this.getStage()
        this.getImages()
    },

    methods:{
        getStage()
        {
            this.myStage = this.stage[0];
        },

        getImages()
        {
            this.myImages = this.images;
        },

        toggleImgsContainer()
        {
            this.imagesContainer.visible = !this.imagesContainer.visible;
        },

        async addStageImg(event)
        {
            this.loading = true;

            const file = event.target.files[0];
            const id = this.myStage.id;

            const formData = new FormData();
            formData.append('url', file);
            formData.append('stage_id', id);

            await axios.post('/admin/images', formData, {
                headers:{
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'multipart/form-data'
                }
            })
            
            this.store.forceReload();
        },


        nextImg()
        {
            let index = this.currentImgIndex;
            const minIndex = 0;
            const maxIndex = this.images.length -1;
            if(index === maxIndex)
            {
                this.currentImgIndex = minIndex
            }
            else
            {
                this.currentImgIndex++
            }

        },

        prevImg()
        {
            let index = this.currentImgIndex;
            const minIndex = 0;
            const maxIndex = this.images.length -1;

            if(index === minIndex)
            {
                this.currentImgIndex = maxIndex;
            }
            else
            {
                this.currentImgIndex--            
            }
        },
    }
}

</script>

<template>
    <section v-if="myStage !== null && myImages !== null" class="py-5 ">
        <div class="container">
            <h3 class="slide-container">
                <a :href="'/admin/days/'+dayId">
                    <i class="bi bi-arrow-return-left slide"></i> Torna al Giorno <strong>{{ dayDate }}</strong>
                </a>
                <a :href="'/admin/days/showOne/'+tripId">
                    <i class="bi bi-arrow-return-left slide"></i> Torna al Viaggio <strong>{{ tripName }}</strong>
                </a>
            </h3>

            <div class="my-container-small">
                <img :src="myStage.cover" alt="">
                <h1>
                    {{ myStage.name }}
                </h1>
                <p>{{ myStage.description }}</p>
            </div>

            <div class="d-flex justify-content-center">
                <h3 class="show-imgs btn btn-outline-coral" @click="toggleImgsContainer">Mostra Immagini del Viaggio:</h3>
            </div>
            <div class="imgs-container" v-if="imagesContainer.visible">
                <p v-if="this.loading" class="text-warning">Caricamento in corso...</p>
                <label v-else for="image" class="ms-3 mt-1 upload-image-icon"> <i class="bi bi-cloud-arrow-up-fill"></i></label>
                <input @change="addStageImg" type="file" name="image" id="image" accept=".jpg, .jpeg, .png, .bmp, .svg, .webp" hidden>
                <div class="row g-5 d-none d-lg-flex" v-if="images.length > 0" >
                    <div v-for="image in images" class="col-md-6 col-lg-3 text-white d-flex justify-content-center align-items-center">
                        <div class="card">
                            <img :src="image.url" alt="Immagine Corrotta">
                        </div>
                    </div>
                </div>
                <div class="row g-5 d-block d-md-none">
                    <div class="col-12 text-white d-flex justify-content-around align-items-center">
                        <i class="bi bi-chevron-compact-left change-img" @click="prevImg"></i>
                        <div class="card">
                            <img :src="getImgUrl" alt="Immagine Corrotta">
                        </div>
                        <i class="bi bi-chevron-compact-right change-img" @click="nextImg"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>


<style lang="scss" scoped>
// animazioni
.slide-container{
    max-width: fit-content;
    display: flex;
    flex-direction: column;
    row-gap: 5px;
    margin-bottom: 15px;
    .slide{
        display: inline-block;
        animation: slide 2s linear infinite;
    }

    @keyframes slide {
        0%{
            transform: translateX(-15px);
        }
    
        50%{
            transform: translateX(3px);
        }
    
    
        100%{
            transform: translateX(-15px);
        }    
    }
}

.upload-image-icon{
    color: rgba(255, 127, 80, 1);
    font-size: 1.5rem;
    cursor: pointer;
    animation: pulse 3s linear infinite;

    @keyframes pulse {
        0%{
            font-size: 1.5rem;
            color: rgba(255, 127, 80, 0.3);
        }
        50%{
            font-size: 1.5rem;
            color: rgba(255, 127, 80, 1);
        }
        100%{
            font-size: 1.5rem;
            color: rgba(255, 127, 80, 0.3);
        }
    }
}




.my-container-small{
    max-width: 600px;
    margin: 0 auto;
}


.show-imgs{
    margin-top: 30px;
    margin-left: 10px;
    cursor: pointer;
}

.imgs-container{
    border: 5px solid coral;
    border-radius: 18px;
    background-color: rgb(33, 33, 33);
    min-height: 300px;
}

.change-img{
    color:  coral;
    cursor: pointer;
    font-size: 1.5rem;
}

.card{
    width: 250px;
    height: 190px;
    img{
        width: 248px;
        height: 188px;
        border-radius: 8px;
    }
}
</style>