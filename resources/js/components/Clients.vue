<template>
  <v-data-table
    :loading = "loading"
    loading-text="Cargando datos..."
    :headers="headers"
    :items="clients"
    sort-by="id"
    class="elevation-1"
  >
    <template v-slot:top>
      <v-card flat color="teal lighten-3">
        <v-card-title>
          Clientes
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
        <v-card
          class="d-flex flex-row-reverse mx-auto"
        >
          <v-dialog v-model="dialog" max-width="500px">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              right
              color="teal darken-3"
              class="mb-1 white--text"
              fab
              v-bind="attrs"
              v-on="on"
              >
              <v-icon dark>mdi-account-plus</v-icon>
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="12">
                      <v-text-field color="teal" v-model="editedItem.name" label="Nombre"></v-text-field>
                  </v-col>
                  <v-col cols="12">
                      <v-text-field color="teal" v-model="editedItem.phone" label="Teléfono"></v-text-field>
                  </v-col>
                  <v-col cols="12">
                      <v-text-field color="teal" v-model="editedItem.address" label="Dirección"></v-text-field>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="teal darken-1" text @click="close">Cancelar</v-btn>
              <v-btn color="teal darken-1" text @click="save">Guardar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        
        </v-card>
        
      </v-card>
    </template>
    <template v-slot:item.actions="{ item }">
      <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
      <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
    </template>
    <template v-slot:no-data>
      <v-btn color="teal" @click="initialize">Reset</v-btn>
    </template>
  </v-data-table>
</template>


<script>
  export default {
    data: () => ({
      dialog: false,
      loading: false,
      search: '',
      headers: [
        { text: '#', align: 'start', sortable: false, value: 'id', },
        { text: 'Nombre', value: 'name', sortable: true, },
        { text: 'Teléfono', value: 'phone' },
        { text: 'Dirección', value: 'address' },
        { text: 'Acciones', value: 'actions', sortable: false },
      ],
      clients: [],
      editedIndex: -1,
      editedItem: {
        id: '',
        name: '',
        phone: '',
        address: '',
      },
      defaultItem: {
        id: '',
        name: '',
        phone: '',
        address: '',
      },
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Nuevo Cliente' : 'Editar Cliente'
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
        axios.get('/api/clients', {})
          .then(res => this.clients = res.data.clients)
      },

      editItem (item) {
        this.editedIndex = this.clients.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.clients.indexOf(item)
        let decide = confirm('¿Confirma que desea eliminar este cliente?')
        if(decide){
          axios.delete('/api/clients/' + item.id)
          .then(res => {
            this.clients.splice(index, 1)
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
            axios.put('/api/clients/' + this.editedItem.id, {
              'name': this.editedItem.name,
              'phone': this.editedItem.phone,
              'address': this.editedItem.address})
              .then(res => Object.assign(this.clients[this.editedIndex], res.data.client))
              .catch(err => console.log(err.response))
          } else {
            axios.post('/api/clients', {
              'name': this.editedItem.name,
              'phone': this.editedItem.phone,
              'address': this.editedItem.address
              })
              .then(res => this.clients.push(res.data.client))
              .catch(err => console.dir(err.response))
            }
        this.close()
      },
    },
  }
</script>