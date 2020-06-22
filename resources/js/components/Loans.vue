<template>
  <v-data-table
    :loading = "loading"
    loading-text="Cargando datos..."
    :headers="headers"
    :items="loans"
    :search="search"
    sort-by="id"
    class="elevation-1"
  >
    <template v-slot:top>
      <v-card flat color="teal lighten-3">
        <v-card-title>
          Préstamos
          <v-spacer></v-spacer>
          <v-text-field
            v-model="search"
            color = "teal"
            append-icon="mdi-magnify"
            label="Buscar"
            single-line
            hide-details
          ></v-text-field>
        </v-card-title>
        <v-card class="d-flex flex-row-reverse mx-auto">
          <v-dialog v-model="dialog" max-width="500px">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              right
              color="teal darken-3"
              class="mb-1 white--text"
              fab
              v-bind="attrs"
              v-on="on">
              <v-icon dark>mdi-account-cash</v-icon>
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-row>
                    <v-col class="d-flex" cols="12" sm="12">
                        <v-autocomplete
                        color="teal"
                        :items="clients"
                        label="Cliente"
                        v-model="editedItem.client"
                        outlined
                        prepend-icon="mdi-account"
                        required></v-autocomplete>
                    </v-col>
                    <v-col class="d-flex" cols="12" sm="12">
                        <v-text-field
                        color="teal"
                        outlined
                        v-model="editedItem.amount"
                        label="Cantidad"
                        type="number"
                        prepend-icon="mdi-currency-usd"
                        required></v-text-field>
                    </v-col>
                    <v-col class="d-flex" cols="12" sm="12">
                        <v-text-field
                        color="teal"
                        outlined
                        v-model="editedItem.payments_n"
                        label="Número de pagos"
                        type="number"
                        prepend-icon="mdi-pound-box"
                        required></v-text-field>
                    </v-col>
                    <v-col class="d-flex" cols="12" sm="12">
                        <v-text-field
                        color="teal"
                        outlined
                        v-model="editedItem.quota"
                        label="Cuota"
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
              <v-btn type="submit" color="teal darken-1" text @click="save">Guardar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        </v-card>
      </v-card>
    </template>
    <template v-slot:item.actions="{ item }">
        <router-link :to="{name: 'loan', params: {id: item.id}}">
            <v-icon small class="mr-2">mdi-eye</v-icon>
        </router-link>
      <v-icon v-if="item.finished == false" small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
      <v-icon v-if="item.finished == true" small @click="deleteItem(item)">mdi-delete</v-icon>
    </template>
    <template v-slot:no-data>
      <v-btn color="teal" @click="initialize">Refrescar</v-btn>
    </template>
  </v-data-table>
</template>

<script>
  export default {
    data: () => ({
      dialog: false,
      loading: false,
      search: '',
      clients: [],
      headers: [
        { text: '#', align: 'start', sortable: false, value: 'id', },
        { text: 'Nombre', value: 'client', sortable: true, },
        { text: 'Cantidad Prestada', value: 'amount' },
        { text: 'Total a pagar', value: 'total' },
        { text: 'Fecha de Ministración', value: 'ministering_date' },
        { text: 'Acciones', value: 'actions', sortable: false },
      ],
      loans: [],
      editedIndex: -1,
      editedItem: {
        id: '',
        client_id: '',
        amount: '',
        payments_n: '',
        quota: '',
        total: '',
        ministering_date: '',
        due_date: ''
      },
      defaultItem: {
        id: '',
        client_id: '',
        amount: '',
        payments_n: '',
        quota: '',
        total: '',
        ministering_date: '',
        due_date: ''
      },
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Nuevo Préstamo' : 'Editar Préstamo'
      },
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
    },

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
        axios.get('/api/loans', {})
          .then(res => {
            this.loans = res.data.loans,
            this.clients = res.data.clients})
      },

      editItem (item) {
        this.editedIndex = this.loans.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.loans.indexOf(item)
        let decide = confirm('¿Confirma que desea eliminar este préstamo?')
        if(decide){
          axios.delete('/api/loans/' + item.id)
          .then(res => {
            this.loans.splice(index, 1)
            })
            .catch(err => console.log(err.response))
        }
        
      },

      close () {
        this.dialog = false
        setTimeout(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        }, 300)
      },

      save () {
        if (this.editedIndex > -1) {
            axios.put('/api/loans/' + this.editedItem.id, {
              'client': this.editedItem.client,
              'amount': this.editedItem.amount,
              'payments_n': this.editedItem.payments_n,
              'quota': this.editedItem.quota,
              })
              .then(res => Object.assign(this.loans[this.editedIndex], res.data.loan),)
              .catch(err => console.log(err.response))
          } else {
            axios.post('/api/loans', {
              'client': this.editedItem.client,
              'amount': this.editedItem.amount,
              'payments_n': this.editedItem.payments_n,
              'quota': this.editedItem.quota,
            })
            .then(res => this.loans.push(res.data.loan))
            .catch(err => console.dir(err.response))
            this.initialize()
          }
        this.close()
      },
    },
  }
</script>