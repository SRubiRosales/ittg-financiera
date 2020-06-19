<template>
  <v-list dense>
    <v-list-item link to="/">
      <v-list-item-action>
        <v-icon>mdi-home</v-icon>
      </v-list-item-action>
      <v-list-item-content>
        <v-list-item-title>Inicio</v-list-item-title>
      </v-list-item-content>
    </v-list-item>
    <v-list-item link to="/about">
      <v-list-item-action>
        <v-icon>mdi-information</v-icon>
      </v-list-item-action>
      <v-list-item-content>
        <v-list-item-title>Acerca de</v-list-item-title>
      </v-list-item-content>
    </v-list-item>
    <v-list-item link to="/clients">
      <v-list-item-action>
        <v-icon>mdi-account-box</v-icon>
      </v-list-item-action>
      <v-list-item-content>
        <v-list-item-title>Clientes</v-list-item-title>
      </v-list-item-content>
    </v-list-item>
    <v-list-item link to="/loans">
      <v-list-item-action>
        <v-icon>mdi-cash-usd</v-icon>
      </v-list-item-action>
      <v-list-item-content>
        <v-list-item-title>Préstamos</v-list-item-title>
      </v-list-item-content>
    </v-list-item>
  </v-list>
</template>