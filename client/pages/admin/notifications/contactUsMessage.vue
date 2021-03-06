<template>
    <v-container>
        <v-card-title>

            <v-spacer></v-spacer>
            <v-text-field
                    v-model="search"
                    clearable
                    :label="`${$t('form.search')}..`"
                    type="text"
                    @keyup="keyupHandle"
                    @click:clear="keyupHandle"
            >
                <v-tooltip
                        slot="prepend"
                        bottom
                >
                    <v-icon slot="activator">
                        mdi-help-circle-outline
                    </v-icon>
                    Loading ...
                </v-tooltip>

                <v-fade-transition slot="append">
                    <v-progress-circular
                            v-if="loading"
                            size="24"
                            color="info"
                            indeterminate
                    ></v-progress-circular>
                    <v-icon
                            v-else
                    >
                        search
                    </v-icon>
                </v-fade-transition>
            </v-text-field>
        </v-card-title>
        <v-data-table
                :headers="headers"
                :items="messages"
                :pagination.sync="tablePagination"
                :total-items="pagination.total"
                :rows-per-page-items="rowsPerPageItems"
                :loading="loading"
                class="elevation-1"
        >
            <template
                    slot="items"
                    slot-scope="props"
            >
                <td>{{ props.item.name }}</td>
                <td>{{ props.item.subject }}</td>
                <td>
                    <v-flex xs12 class="text-xs-center">
                        <v-btn
                                outline
                                small
                                fab
                                color="primary"
                                @click="showMessage(props.item)"
                        >
                            <v-icon>remove_red_eye</v-icon>
                        </v-btn>
                        <v-btn
                                outline
                                small
                                fab
                                color="green"
                                @click="checking(props.item)"
                        >
                            <v-icon>check</v-icon>
                        </v-btn>

                        <v-btn
                                outline
                                small
                                fab
                                color="red"
                                @click="trashing(props.item)"
                        >
                            <v-icon>delete</v-icon>
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
                    {{$t('btns.remove')}}
                </v-card-title>conf_remove
                <v-card-text>  {{$t('conf_remove')}}</v-card-text>
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


        <!--checkdialog dialog-->
        <v-dialog
                v-model="checkdialog"
                persistent
                max-width="290"
        >
            <v-card>
                <v-card-title class="headline">
                    {{$t('check')}}
                </v-card-title>
                <v-card-text>{{$t('conf_check')}}</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                            color="green darken-1"
                            flat
                            @click="checkdialog = false"
                    >
                        {{$t('btns.close')}}
                    </v-btn>
                    <v-btn
                            :loading="form.busy"
                            color="red darken-1"
                            flat
                            @click="checkMessage"
                    >
                        {{$t('btns.confirm')}}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!--remove d end-->

        <!--showMessage dialog-->
        <v-dialog v-model="showdialog" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    <v-layout justify-center pa-0 ma-0>
                        <v-flex xs12 class="headline">
                            <v-card dark class="pa-3" color="primary">Message</v-card>
                        </v-flex>
                    </v-layout>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md pa-0 ma-0>
                        <v-layout wrap class="text-xs-justify">
                            <v-flex xs12>
                                <v-text-field
                                        :value="form.name"
                                        :label="$t('form.from')"
                                        readonly
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                            <v-text-field
                                    :value="form.email"
                                    :label="$t('form.email')"
                                    readonly
                            ></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                            <v-text-field
                                    :value="form.subject"
                                    :label="$t('form.subject')"
                                    readonly
                            ></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea
                                        readonly
                                        box
                                        :label="$t('form.message')"
                                        :value="form.message"
                                ></v-textarea>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn outline color="green darken-1" flat @click="openReplyDialog">{{$t('btns.reply')}}</v-btn>
                    <v-btn outline color="blue darken-1" flat @click="showdialog = false">{{$t('btns.close')}}</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!--end showMessage dialog-->

        <!--replydialog dialog-->
        <v-dialog v-model="replydialog" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    <v-layout justify-center pa-0 ma-0>
                        <v-flex xs12 class="headline">
                            <v-card dark class="pa-3" color="primary">{{$t('reply_msg')}}</v-card>
                        </v-flex>
                    </v-layout>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md pa-0 ma-0>
                        <v-layout wrap class="text-xs-justify">
                            <v-flex xs12>
                                <v-text-field
                                        :value="replyForm.to"
                                        :label="$t('form.from')"
                                        readonly
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea
                                        readonly
                                        box
                                        :label="$t('form.message')"
                                        :value="form.message"
                                ></v-textarea>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field
                                        v-model="replyForm.subject"
                                        :label="$t('form.subject')"
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea
                                        v-model="replyForm.message"
                                        box
                                        :label="$t('reply_msg')"
                                ></v-textarea>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn outline color="red darken-1" :loading="replyForm.busy" flat @click="replyToMessage">{{$t('btns.reply')}}</v-btn>
                    <v-btn outline color="blue darken-1" flat @click="replydialog = false">{{$t('btns.close')}}</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!--end replydialog dialog-->
    </v-container>
</template>

<script>
    import Form from 'vform'

    export default {
        name: "message",
        layout: "admin",
        head() {
            return {
                title: this.$t('navbar.home'),
            }
        },

        async asyncData({$axios, $router}) {
            try {
                let archives = await $axios.get('contact/archive')
                let category = await $axios.get('contact')

                return {
                    archives: archives.data.archives,
                    pagination: category.data.meta,
                    messages: category.data.data
                }
            } catch (e) {
                $router.push({name: 'error'})
            }
        },

        data() {
            return {
                first: true,
                loading: false,
                archives: {},
                messages: {},
                page: 1,
                tablePagination: {},
                pagination: {},

                search: '',
                showMessageing: false,
                dialog: false,
                checkdialog:false,
                showdialog: false,

                replydialog:false,

                formObj: {
                    id: '',
                    name: '',
                    subject:'',
                    message:'',
                },
                form: new Form({
                    id: '',
                    name: '',
                    subject:'',
                    message:'',
                }),

                replyForm : new Form({
                    from:'admin@zakarpattour.com',
                    to:'',
                    toName:'',
                    question:'',
                    subject:'',
                    message:''
                }),

                headers: [
                    {text: 'From', align: 'left', sortable: false, value: 'name'},
                    {text: 'Subject', align: 'left', sortable: false, value: 'name'},
                    {text: 'Action', value: 'action', sortable: false, align: 'center'},
                ],
                rowsPerPageItems: [5, 10, 20],
            }
        },
        watch: {
            page: {
                handler() {
                    this.responseMessage()
                }
            },
            'tablePagination.rowsPerPage': {
                handler(val) {
                    if (this.first) {
                        this.first = !this.first
                    } else {
                        this.doPaginate()
                    }
                },
            }
        },
        mounted() {
        },
        computed: {},
        methods: {
            async responseMessage() {
                let url = `contact?page=${this.page}&per_page=${this.tablePagination.rowsPerPage}`
                if (this.search) url += `&q=${this.search}`

                this.loading = true
                const {data} = await this.$axios.get(url)
                this.pagination = data.meta
                this.messages = data.data
                this.loading = false
                console.log('loaded')

            },

            async store(url) {
                const {data} = await this.form.put(url)
                this.form = new Form(this.formObj)
                await this.responseMessage()
                this.showdialog = false
            },

            async trash() {
                const {data} = await this.form.delete('contact/trash')
                this.form = new Form(this.formObj)
                await this.responseMessage()
                this.dialog = false
            },
            async checkMessage(){
                const {data} = await this.form.post('contact/check')
                this.form = new Form(this.formObj)
                await this.responseMessage()
                this.checkdialog = false
            },

            async replyToMessage(){
                try{
                    console.log(this.replyForm)
                   const {data} = await this.replyForm.post('contact/reply')

                    this.replyForm = new Form({
                        from:'admin@zakarpattour.com',
                        to:'',
                        toName:'',
                        subject:'',
                        question:'',
                        message:''
                    })
                    this.replydialog = false
                }catch (e) {
                    console.log(e)
                }
            },

            doPaginate() {
                if (this.page == 1) this.responseMessage()
                this.page = 1
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

            showMessage(item) {
                this.form = new Form(item)
                this.showMessageing = true
                this.showdialog = true
            },
            checking(item){
                this.form = new Form(item)
                this.checkdialog = true
            },
            trashing(item) {
                this.form = new Form(item)
                this.dialog = true
            },


            openReplyDialog(){
                this.replyForm.to = this.form.email
                this.replyForm.toName = this.form.name
                this.replyForm.question = this.form.message

                this.replydialog = true
            }
        },
    }
</script>

<style scoped>

</style>
