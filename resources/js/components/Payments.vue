<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-card color="teal lighten-3">
                <v-card flat color="teal lighten-3">
                    <v-card-title>Resumen de Pagos
                        <v-spacer></v-spacer>
                        
                        <v-btn color="teal darken-3" class="mb-1 white--text" fab @click="exportar()">
                            <v-icon dark>mdi-file-excel</v-icon>
                        </v-btn>
                    </v-card-title>
                </v-card>
                <v-simple-table>
                    <template v-slot:default>
                    <thead>
                        <tr>
                            <th class="text-left">#</th>
                            <th class="text-left">Nombre</th>
                            <th class="text-left">Cantidad Prestada</th>
                            <th class="text-left">Cuota</th>
                            <th class="text-left">Pagos</th>
                            <th class="text-left">Total a pagar</th>
                            <th class="text-left">Saldo abonado</th>
                            <th class="text-left">Saldo pendiente</th>
                            <th class="text-left">Fecha de vencimiento</th>
                            <!--<th class="text-left">Estado</th>-->
                            <th class="text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="l in loans" :key="l.loan_id">
                            <td v-text="l.loan_id"></td>
                            <td v-text="l.client"></td>
                            <td>$ {{ l.cantidad_ministrada }}</td>
                            <td>$ {{ l.cuota }}</td>
                            <td v-text="l.num_de_pagos"></td>
                            <td>$ {{ l.total }} </td>
                            <td v-if="l.paid > 0">$ {{ l.paid }}</td>
                            <td v-else>$0</td>
                            <td>$ {{ l.total - l.paid }}</td>
                            <td v-text="l.fecha_vencimiento"></td>
                            <!--<td v-if="l.finished == false">Deuda activa</td>
                            <td v-else>Deuda saldada</td>-->
                            <td>
                                <router-link :to="{name: 'loan', params: {id: l.loan_id}}">
                                    <v-icon small class="mr-2">mdi-eye</v-icon>
                                </router-link>
                            </td>
                        </tr>
                    </tbody>
                    </template>
                </v-simple-table>
            </v-card>
        </v-flex>
    </v-layout>
</template>

<script>
    import XLSX from 'xlsx';
    export default {
        data: () =>({
            loading: false,
            loans: [],
            payments: [],
        }),

        created () {
            this.initialize()
        },

        methods: {
            initialize () {
                axios.interceptors.request.use((config) => {
                    this.loading = true;
                    return config;
                }, (error) => {
                    this.loading = false;
                    return Promise.reject(error);
                });
                axios.interceptors.response.use((response) => {
                    this.loading = false;
                    return response;
                }, (error) => {
                    this.loading = false;
                    return Promise.reject(error);
                });
                axios.get('/api/payments', {})
                .then(res => {
                    this.loans = res.data.loans,
                    console.log(res.data.payments)
                })
            },

            exportar: function () {
                let data = XLSX.utils.json_to_sheet(this.loans)
                const workbook = XLSX.utils.book_new()
                const filename = 'resumen-de-pagos'
                XLSX.utils.book_append_sheet(workbook, data, filename)
                XLSX.writeFile(workbook, `${filename}.xlsx`)
            }
        },
    }
</script>