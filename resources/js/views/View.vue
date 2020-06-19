<template>
    <v-app>
        <v-navigation-drawer v-model="drawer" app clipped>
            <navigation></navigation>
        </v-navigation-drawer>
        <v-app-bar app clipped-bar color="teal" dense>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>Financiera S.A.</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-menu offset-y>
                <template v-slot:activator="{ on }">
                    <v-btn text v-on="on">
                        {{ user.name }}
                        <v-icon right dark>mdi-chevron-down</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item @click="logout">
                        <v-list-item-icon>
                            <v-icon>mdi-exit-to-app</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title>
                            Logout
                        </v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
    </v-app-bar>
    <v-content>
        <v-container fluid>
            <router-view></router-view>
        </v-container>
    </v-content>
    <v-footer color="teal" app>
      <span class="white--text">&copy; 2020</span>
    </v-footer>
  </v-app>
</template>

<script>
import Navigation from '@/js/components/Navigation';
export default {
    props: {
        source: String,
    },
    data: () => ({
        drawer: null,
    }),
    components: {
        navigation: Navigation
    },
    computed: {
        user () {
            return this.$store.state.user || { name: '' };
        }
    },
    methods: {
        async logout() {
            await this.$store.dispatch('logout');
            this.$router.push('/login');
        }
    }
}
</script>