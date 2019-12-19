<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card" v-if="user">
                    <div class="card-header p-2">
                        Добавить новость...
                    </div>
                    <div class="card-body p-2 justify-content-center">
                        <textarea v-model="newItemText" class="form-control"></textarea>
                        <div v-if="newItemText" class="row justify-content-center">
                            <button @click="addNews" class="mt-2 btn btn-sm btn-primary">Добавить новость</button>
                        </div>
                    </div>
                    <div class="card-header p-2">
                        ... или фотографию
                    </div>
                    <div class="card-body p-2">
                        <input type="file" ref="newPhotoFile" class="form-control" @change="checkNewPhotoFile" accept="image/jpeg,image/png">
                        <div v-if="newPhotoFileSelected" class="row justify-content-center">
                            <button @click="addPhoto" class="mt-2 btn btn-sm btn-primary">Загрузить и добавить</button>
                        </div>
                    </div>
                </div>

                <news-item v-for="(item, key) in feed" :item="item" :key="key" @feedItemChangeState="feedItemChangeState"></news-item>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        data() {
            return {
                feed: [],
                newItemText: '',
                newPhotoFileSelected: false,
            };
        },
        computed: {
            user() {
                return window.Laravel.user;
            }
        },
        methods: {
            addNews() {
                if (!this.newItemText)
                    return;

                axios.post('/api/feed', {
                    type: 'news',
                    text: this.newItemText,
                })
                .then((response) => {
                    this.newItemText = '';
                    this.feed.unshift(response.data);
                    this.updateFeedList();
                });
            },
            addPhoto() {
                if (!this.newPhotoFileSelected)
                    return;

                let formData = new FormData();
                formData.append('type', 'photo');
                formData.append('image', this.$refs.newPhotoFile.files[0]);

                axios.post('/api/photo', formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                )
                .then((response) => {
                    this.$refs.newPhotoFile.files = null;
                    this.newPhotoFileSelected = false;
                    this.feed.unshift(response.data);
                    this.updateFeedList();
                });

            },
            checkNewPhotoFile() {
                this.newPhotoFileSelected = this.$refs.newPhotoFile.files.length > 0;
            },
            updateFeedList() {
                axios.get('/feed')
                .then((response) => {
                    this.feed = response.data.data;
                });
            },
            feedItemChangeState(state, item, index) {
                if (item.user_id === window.Laravel.user.id) {
                    if (state === 'delete') {
                        this.feed.splice(index, 1);
                        return;
                    }
                }
                if (state === 'reload') {
                    this.feed.splice(index, 1, item);
                }
            },
        },
        mounted() {
            this.updateFeedList();
        }
    }
</script>
