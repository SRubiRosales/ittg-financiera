<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-card color="teal lighten-3">
                <v-container fluid grid-list-lg>
                    <v-layout row>
                        <v-flex v-bind="loan">
                            <div class="headline">Préstamo a {{ loan.name }} </div>
                            <ul>
                                    <li>Número de préstamo: {{ loan.loan_id }}</li>
                                    <li>Cantidad ministrada: $ {{ loan.amount }}</li>
                                    <li>Total a pagar: $ {{ loan.total }}</li>
                                    <li v-if="loan.paid > 0">Total abonado: $ {{ loan.paid }} </li>
                                    <li v-else>Total abonado: $ 0</li>
                                    <li>Saldo pendiente: $ {{ loan.total - loan.paid }}</li>
                                    <li>Cuota: $ {{ loan.quota }}</li>
                                </ul>
                        </v-flex>
                        <v-flex v-bind="loan">
                            <v-dialog v-model="dialog" max-width="300px" v-if="loan.finished == false">
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        rounded
                                        color="teal darken-3"
                                        dark
                                        v-bind="attrs"
                                        v-on="on">Abonar
                                        <v-icon dark>mdi-credit-card-plus-outline</v-icon>
                                    </v-btn>
                                </template>
                                <v-card>
                                    <v-card-text>
                                        <v-container>
                                            <v-row>
                                                <v-col cols="12" sm="12" v-bind="loan">
                                                    <v-text-field
                                                        v-model="loan_id"
                                                        label="Préstamo #"
                                                        value="loan.loan_id"
                                                        disabled
                                                    >Préstamo</v-text-field>
                                                </v-col>
                                                <v-col class="d-flex" cols="12" sm="12">
                                                    <v-text-field
                                                    color="teal"
                                                    outlined
                                                    v-model="cantidad"
                                                    label="Cantidad"
                                                    type="number"
                                                    prepend-icon="mdi-currency-usd"
                                                    required></v-text-field>
                                                </v-col>
                                            </v-row>
                                        </v-container>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn color="teal darken-1" text @click="close">Cancelar</v-btn>
                                        <v-btn type="submit" color="teal darken-1" text @click="abonar">Ok</v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                        </v-flex>
                    </v-layout>
                    <v-snackbar v-model="snackbar" color="teal" :top="y === 'top'">
                        Gracias por su pago
                        <template v-slot:action="{ attrs }">
                        <v-btn
                            text
                            color="teal accent-1"
                            v-bind="attrs"
                            @click="initialize"
                        >
                            Ok
                        </v-btn>
                        </template>
                    </v-snackbar>
                </v-container>
                <v-simple-table>
                    <template v-slot:default>
                    <thead>
                        <tr>
                            <th class="text-left">Número de pago</th>
                            <th class="text-left">Cuota</th>
                            <th class="text-left">Abonado</th>
                            <th class="text-left">Fecha de pago</th>
                            <th class="text-left">Fecha de abono</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in payment" :key="p.id">
                            <td v-text="p.payment_number"></td>
                            <td v-text="p.amount"></td>
                            <td v-text="p.received_amount"></td>
                            <td v-text="p.date_payment"></td>
                            <td v-text="p.updated_at"></td>
                        </tr>
                    </tbody>
                    </template>
                </v-simple-table>
            </v-card>
        </v-flex>
    </v-layout>
</template>

<script>
    export default {
        data: () =>({
            dialog: false,
            snackbar: false,
            y: 'top',
            payment: [],
            paymentOk: [],
            loan: [],
            cantidad: '',
            loan_id: '',
        }),

        created () {
            this.initialize()
        },

        methods: {
            initialize () {
                let id = this.$route.params.id;
                var n_pay;
                axios.interceptors.request.use((config) => {
                    return config;
                }, (error) => {
                    return Promise.reject(error);
                });
                axios.interceptors.response.use((response) => {
                    return response;
                }, (error) => {
                    return Promise.reject(error);
                });
                axios.get('/api/payments/'+ id)
                .then(res => {
                    this.payment = res.data.payment
                    this.loan = res.data.loan
                    this.loan_id = res.data.loan.loan_id
                })
            },

            close () {
                this.dialog = false
                setTimeout(() => {
                this.cantidad = null
                }, 300)
            },

            showSnackbar () {
                this.snackbar = true
            },

            abonar () {
                axios.put('/api/payments/pagar', {
                    'loan_id': this.loan_id,
                    'cantidad': this.cantidad,
                })
                .then(res => {
                    this.payment = res.data.payment,
                    this.loan = res.data.loan})
                this.close();
                this.showSnackbar()
            },
        },
    }
</script>