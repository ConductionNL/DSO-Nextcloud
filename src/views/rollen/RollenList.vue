<script setup>
import { navigationStore, rolStore } from '../../store/store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField
					:value.sync="search"
					:show-trailing-button="search !== ''"
					label="Search"
					class="searchField"
					trailing-button-icon="close"
					@trailing-button-click="clearText">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton @click="fetchData">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton @click="store.setModal('addRoll')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Rol toevoegen
					</NcActionButton>
				</NcActions>
			</div>
			<div v-if="!loading">
				<NcListItem v-for="(rollen, i) in rollenList.results"
					:key="`${rollen}${i}`"
					:name="rollen?.name"
					:active="store.rolId === rollen?.id"
					:details="'1h'"
					:counter-number="44"
					@click="store.setRolItem(rollen)">
					<template #icon>
						<ChatOutline :class="store.rolId === rollen.id && 'selectedZaakIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #subname>
						{{ rollen?.summary }}
					</template>
					<template #actions>
						<NcActionButton @click="editRol(rol)">
							Bewerken
						</NcActionButton>
						<NcActionButton>
							Verwijderen
						</NcActionButton>
					</template>
				</NcListItem>
			</div>
		</ul>

		<NcLoadingIcon v-if="loading"
			class="loadingIcon"
			:size="64"
			appearance="dark"
			name="Rollen aan het laden" />
	</NcAppContentList>
</template>
<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon } from '@nextcloud/vue'

import Magnify from 'vue-material-design-icons/Magnify.vue'

import ChatOutline from 'vue-material-design-icons/ChatOutline.vue'

export default {
	name: 'RollenList',
	components: {
		NcListItem,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		ChatOutline,
		Magnify,
		NcLoadingIcon,
	},
	data() {
		return {
			search: '',
			loading: true,
			rollenList: [],
		}
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		editRol(rol) {
			rolStore.setRolItem(rol)
			rolStore.setRolId(rol.id)
			navigationStore.setModal('editRol')
		},
		fetchData(newPage) {
			this.loading = true
			fetch(
				'/index.php/apps/zaakafhandelapp/api/rollen',
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.rollenList = data
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		clearText() {
			this.search = ''
		},
	},
}
</script>
<style>
.listHeader {
    position: sticky;
    top: 0;
    z-index: 1000;
    background-color: var(--color-main-background);
    border-bottom: 1px solid var(--color-border);
}

.searchField {
    padding-inline-start: 65px;
    padding-inline-end: 20px;
    margin-block-end: 6px;
}

.selectedZaakIcon>svg {
    fill: white;
}

.loadingIcon {
    margin-block-start: var(--zaa-margin-20);
}
</style>
