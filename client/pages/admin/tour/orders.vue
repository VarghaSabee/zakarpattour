<template>
    <v-container>
        <v-card-title  class="title font-weight-bold">
            {{$t('navbar.orders')}}
        </v-card-title>
        <v-data-table
                :headers="headers"
                :items="categories"
                :pagination.sync="tablePagination"
                :total-items="pagination.total"
                :rows-per-page-items="rowsPerPageItems"
                :loading="loading"
                class="elevation-1"
        >
            <template
                    slot="items"
                    slot-scope="props"
                    v-if="props.item.id !== 0"
            >
                <td>{{getTitle(props.item.orders[0].tour) }}</td>
                <td>{{ props.item.total_price }}</td>
                <td>{{ props.item.updated_at }}</td>
                <td>
                    <v-flex xs12 class="text-xs-center">
                        <v-btn
                                outline
                                small
                                fab
                                color="indigo"
                        >
                            <v-icon>done_all</v-icon>
                        </v-btn>
                    </v-flex>
                </td>
            </template>

            <template slot="no-data">
                <v-alert :value="true" color="error" icon="warning">
                   {{$t('messages.no_data')}}
                </v-alert>
            </template>
        </v-data-table>
        <div class="text-xs-center pt-2">
            <v-pagination
                    v-model="page"
                    :length="pagination.last_page"
                    :total-visible="7"
                    circle
            ></v-pagination>
        </div>
        <!--remove dialog-->
        <v-dialog
                v-model="dialog"
                persistent
                max-width="290"
        >
            <v-card>
                <v-card-title class="headline">
                    {{$t('category.remove')}}
                </v-card-title>
                <v-card-text>{{$t('category.remove_msg')}}</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                            color="green darken-1"
                            flat
                            @click="dialog = false"
                    >
                        {{$t('btns.close')}}
                    </v-btn>
                    <v-btn
                            :loading="form.busy"
                            color="red darken-1"
                            flat
                            @click="trash"
                    >
                        {{$t('btns.remove')}}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!--remove d end-->

        <!--edit dialog-->
        <v-dialog v-model="savedialog" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    <v-layout justify-center>
                        <span class="headline">{{editing ? $t('category.edit') : $t('category.new')}}</span>
                    </v-layout>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout wrap>
                            <v-flex xs12>
                                <v-form>
                                    <v-text-field label="Name Eng"
                                                  v-model="form.translations[0].name"
                                                  required
                                                  :counter="30"
                                                  :maxlength="30"
                                                  clearable
                                    ></v-text-field>
                                    <v-text-field label="Name Hun"
                                                  v-model="form.translations[1].name"
                                                  :counter="30"
                                                  :maxlength="30"
                                                  clearable

                                    ></v-text-field>
                                    <v-text-field label="Name Ukr"
                                                  v-model="form.translations[2].name"
                                                  :counter="30"
                                                  :maxlength="30"
                                                  clearable

                                    ></v-text-field>
                                </v-form>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn outline color="blue darken-1" flat @click="savedialog = false">{{$t('btns.close')}}</v-btn>
                    <v-btn :loading="form.busy" outline color="green darken-1" flat @click="saveCategory">{{$t('btns.save')}}</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!--end edit dialog-->
    </v-container>
</template>
<script>
    import Form from 'vform'

    export default {
        name: "adminTourCategory",
        layout: "admin",

        head() {
            return {
                title: this.$t('navbar.home'),
            }
        },

        async asyncData({$axios, redirect}) {
            try {
                let data = await $axios.$post('order',{status:"processing"})
                return {
                    pagination: data.meta,
                    categories: data.data
                }
            } catch (e) {
             redirect('/error')
            }
        },

        data() {
            return {
                first: true,
                loading: false,
                categories: [],
                page: 1,
                tablePagination: {},
                pagination: {},

                search: '',
                editing: false,
                dialog: false,
                savedialog: false,

                formObj: {
                    id: '',
                    icon: '',
                    translations: [
                        {locale: 'en', name: ''},
                        {locale: 'hu', name: ''},
                        {locale: 'ua', name: ''},
                    ],
                },
                form: new Form({
                    id: '',
                    icon: '',
                    translations: [
                        {locale: 'en', name: ''},
                        {locale: 'hu', name: ''},
                        {locale: 'ua', name: ''},
                    ],
                }),

                headers: [
                    {text: 'Name', align: 'left', sortable: false, value: 'name'},
                    {text: 'Total price', align: 'left', sortable: false, value: 'total_price'},
                    {text: 'Added at', align: 'left', sortable: false, value: 'updated_at'},
                    {text: 'Action', value: 'action', sortable: false, align: 'center'},
                ],
                rowsPerPageItems: [5, 10, 20],
            }
        },
        watch: {
            page: {
                handler() {
                    this.responseCategory()
                }
            },
            'tablePagination.rowsPerPage': {
                handler(val) {
                    if (this.first) {this.first = !this.first} else {
                        this.doPaginate()
                    }
                },
            }
        },
        mounted() {
        },

        methods: {
            async responseCategory() {
                let url = `tour/category?page=${this.page}&per_page=${this.tablePagination.rowsPerPage}`
                if (this.search) url += `&q=${this.search}`

                this.loading = true
                const {data} = await this.$axios.get(url)
                this.pagination = data.meta
                this.categories = data.data
                this.loading = false
                console.log('loaded')

            },

            async store(url) {
                const {data} = await this.form.put(url)
                this.form = new Form(this.formObj)
                await this.responseCategory()
                this.savedialog = false
            },

            async trash() {
                const {data} = await this.form.delete('tour/category/trash')
                this.form = new Form(this.formObj)
                await this.responseCategory()
                this.dialog = false
            },
            doPaginate() {
                if (this.page == 1) this.responseCategory()
                this.page = 1
            },
            saveCategory() {
                let url = 'tour/category/store';
                if (this.editing) url = 'tour/category/edit';
                this.store(url)
            },

            keyupHandle(event) {
                clearTimeout(this.timeoutId);
                if (event.key == 'Enter') {
                    this.doPaginate();
                    return;
                }
                this.timeoutId = setTimeout(this.doPaginate, 700);
            },

            autoFilter(item, queryText, itemText) {
                return item.name.toLocaleLowerCase().indexOf(queryText.toLocaleLowerCase()) > -1
            },
            autoValue(value) {
                return value.value
            },

            createNew() {
                this.form = new Form(this.formObj)
                this.savedialog = true
                this.editing = false

            },
            edit(item) {
                this.form = new Form(item)
                this.editing = true
                this.savedialog = true
            },
            trashing(item) {
                this.form = new Form(item)
                this.dialog = true
            },
            getTitle(item) {
                return item.translations.find(obj => obj.locale ===  this.getLocal).title
            },
        },
        computed: {
            getLocal(){
                return this.$i18n.locale
            }
        }
    }
</script>
<style scoped>
    .v-icon {
        font-size: 16px;
    }
</style>

