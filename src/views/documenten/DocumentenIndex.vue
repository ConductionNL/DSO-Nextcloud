<script setup>
import { navigationStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<DocumentenList />
		</template>
		<template #default>
			<NcEmptyContent v-if="!store.item || navigationStore.selected != 'zaken' "
				class="detailContainer"
				name="Geen document"
				description="Nog geen document geselecteerd">
				<template #icon>
					<BriefcaseAccountOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="navigationStore.setModal('zakenAdd')">
						Document toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<DocumentDetails v-if="store.item && navigationStore.selected === 'zaken'" :document-id="store.documentItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import DocumentenList from './DocumentenList.vue'
import DocumentDetails from './DocumentDetails.vue'

import BriefcaseAccountOutline from 'vue-material-design-icons/BriefcaseAccountOutline.vue'

export default {
	name: 'DocumentenIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		DocumentenList,
		DocumentDetails,
		BriefcaseAccountOutline,
	},
	data() {
		return {
			activeMetaData: false,
			metaDataId: undefined,
		}
	},
}
</script>
