<template>
    <div class="card mt-4" v-if="item && item.feedable">
        <div class="card-body">
            <template v-if="!editor">
                <template v-if="item.feedable_type === 'news'">
                    {{ item.feedable.text }}
                </template>
                <template v-if="item.feedable_type === 'photo'">
                    <img :src="item.feedable.url" />
                </template>
            </template>
            <template v-else>
                <template v-if="item.feedable_type === 'news'">
                    <textarea v-model="newTextItem" class="form-control"></textarea>
                </template>
                <template v-if="item.feedable_type === 'photo'">
                    <input type="file" ref="newPhotoFile" class="form-control" accept="image/jpeg,image/png">
                </template>
                <div class="row justify-content-center">
                    <button @click="saveFeedItem" class="mt-2 btn btn-sm btn-primary">Изменить</button>
                </div>
            </template>
        </div>
        <div class="card-footer">
            {{ item.created_at }} <strong v-if="item.user">{{ item.user.name }}</strong>
            <template v-if="canEdit" class="card-header py-1">
                <template v-if="!editor">
                    <a href="#" v-on:click.prevent="editFeedItem()" class="text-primary">Изменить</a>
                    <a href="#" v-on:click.prevent="deleteFeedItem()" class="text-danger">Удалить</a>
                </template>
                <template v-else>
                    <a href="#" v-on:click.prevent="cancelEditFeedItem()" class="text-primary">Отменить</a>
                </template>
            </template>
            <div class="float-right d-inline-flex">
                <div class="mt-1 mr-2"><span v-if="item.like_counter > 0">{{ item.like_counter }}</span></div>
                <a v-if="user" href="#" v-on:click.prevent="toggleLike()"><i class="fa fa-2x text-primary" :class="{'fa-heart-o':!item.has_like, 'fa-heart':item.has_like}"></i></a>
                <i v-else class="fa fa-2x text-primary" :class="{'fa-heart-o':!item.has_like, 'fa-heart':item.has_like}"></i>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            item: Object,
        },
        data() {
            return {
                editor: false,
                newTextItem: '',
            };
        },
        computed: {
            canEdit() {
                return this.user && this.item.user_id === this.user.id;
            },
            user() {
                return window.Laravel.user;
            }
        },
        methods: {
            deleteFeedItem() {
                axios.delete('/api/feed/' + this.item.id)
                .then(() => {
                    this.$emit('feedItemChangeState', 'delete', this.item, this.$vnode.key);
                });
            },
            editFeedItem() {
                if (this.item.feedable.text) {
                    this.newTextItem = this.item.feedable.text;
                }
                this.editor = true;
            },
            cancelEditFeedItem() {
                this.editor = false;
            },
            saveFeedItem() {
                if (!this.canEdit)
                    return;

                if (this.item.feedable_type === 'news') {
                    axios.put('/api/feed/' + this.item.id, {
                        type: 'news',
                        text: this.newTextItem,
                    })
                    .then((response) => {
                        this.$emit('feedItemChangeState', 'reload', response.data, this.$vnode.key);
                        this.cancelEditFeedItem();
                    });
                }
                else if (this.item.feedable_type === 'photo') {
                    if (this.$refs.newPhotoFile.files.length === 0)
                        return;

                    let formData = new FormData();
                    formData.append('_method', 'PUT');
                    formData.append('type', 'photo');
                    formData.append('image', this.$refs.newPhotoFile.files[0]);

                    axios.post('/api/feed/' + this.item.id, formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }
                    )
                    .then((response) => {
                        this.$emit('feedItemChangeState', 'reload', response.data, this.$vnode.key);
                        this.cancelEditFeedItem();
                    });
                }

            },
            toggleLike() {
                axios.post('/api/feed/' + this.item.id + '/like', {})
                .then((response) => {
                    this.$emit('feedItemChangeState', 'reload', response.data, this.$vnode.key);
                });
            },
        },
    }
</script>
