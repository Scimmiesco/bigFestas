<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import { Save, X, Plus } from 'lucide-vue-next';
import { computed } from 'vue';

// Usando o 'c' minúsculo conforme corrigimos anteriormente
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import itemsRoutes from '@/routes/items/index';

const props = defineProps<{
    item?: any;
    itemIds?: number[]; // NOVO: Aceita múltiplos IDs para edição em lote
    enums?: any;
    teamSlug: string;
}>();

const emit = defineEmits(['cancel', 'success']); // Emite eventos para fechar o form inline

const isSingleEdit = computed(() => !!props.item);
const isBulkEdit = computed(() => !!props.itemIds && props.itemIds.length > 0);
const isEdit = computed(() => isSingleEdit.value || isBulkEdit.value);

const form = useForm({
    ids: props.itemIds ?? [], // Usado apenas no modo Lote
    nome: props.item?.nome ?? '',
    tipo: props.item?.tipo ?? '',
    status: props.item?.status ?? '',
    quantidade: 1,
    observacoes: props.item?.observacoes ?? '',
});

const submit = () => {
    if (isBulkEdit.value) {
        form.put(`/${props.teamSlug}/itens/lote`, {
            preserveScroll: true,
            onSuccess: () => emit('success'),
        });
    } else if (isSingleEdit.value) {
        form.put(`/${props.teamSlug}/itens/${props.item.id}`, {
            preserveScroll: true,
        });
    } else {
        form.post(`/${props.teamSlug}/itens/`, { preserveScroll: true });
    }
};
</script>

<template>
    <div class="form-container">
        <div v-if="isBulkEdit" class="info-container">
            <strong>Modo de Edição em Lote:</strong> Preencha apenas os campos
            que deseja alterar para todos os {{ form.ids.length }} itens
            selecionados. Deixe em branco os campos que devem manter seus
            valores originais.
        </div>

        <form @submit.prevent="submit" class="form-wrapper">
            <div>
                <label for="nome" class="form-label">
                    Nome do Item
                    <span v-if="!isBulkEdit" class="form-required-asterisk"
                        >*</span
                    >
                </label>
                <input
                    id="nome"
                    type="text"
                    class="form-input"
                    v-model="form.nome"
                    :placeholder="
                        isBulkEdit
                            ? 'Deixe em branco para manter o original'
                            : 'Ex: Cadeira Plástica Branca'
                    "
                    :required="!isBulkEdit"
                    :autofocus="!isBulkEdit"
                />
                <InputError :message="form.errors.nome" class="mt-2" />
            </div>

            <div v-if="!isEdit">
                <label for="quantidade" class="form-label">Quantidade</label>
                <input
                    id="quantidade"
                    type="number"
                    min="1"
                    max="200"
                    v-model="form.quantidade"
                    class="form-input"
                    required
                />
                <InputError :message="form.errors.quantidade" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="tipo" class="form-label">
                        Tipo
                        <span v-if="!isBulkEdit" class="form-required-asterisk"
                            >*</span
                        >
                    </label>
                    <select
                        id="tipo"
                        v-model="form.tipo"
                        class="form-input"
                        :required="!isBulkEdit"
                    >
                        <option value="" :disabled="!isBulkEdit">
                            {{
                                isBulkEdit
                                    ? 'Manter original'
                                    : 'Selecione um tipo'
                            }}
                        </option>
                        <option
                            v-for="tipo in enums?.item_types"
                            :key="tipo.value"
                            :value="tipo.value"
                        >
                            {{ tipo.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.tipo" class="mt-2" />
                </div>

                <div>
                    <label for="status" class="form-label">
                        Status
                        <span v-if="!isBulkEdit" class="form-required-asterisk"
                            >*</span
                        >
                    </label>
                    <select
                        id="status"
                        v-model="form.status"
                        class="form-input"
                        :required="!isBulkEdit"
                    >
                        <option value="" :disabled="!isBulkEdit">
                            {{
                                isBulkEdit
                                    ? 'Manter original'
                                    : 'Selecione um status'
                            }}
                        </option>
                        <option
                            v-for="status in enums?.item_status"
                            :key="status.value"
                            :value="status.value"
                        >
                            {{ status.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-2" />
                </div>
            </div>

            <div>
                <label for="observacoes" class="form-label"
                    >Observações (Opcional)</label
                >
                <textarea
                    id="observacoes"
                    v-model="form.observacoes"
                    rows="3"
                    class="form-input"
                    :placeholder="
                        isBulkEdit
                            ? 'Deixe em branco para manter original'
                            : 'Detalhes adicionais...'
                    "
                ></textarea>
                <InputError :message="form.errors.observacoes" class="mt-2" />
            </div>

            <div class="form-actions">
                <Button
                    v-if="isBulkEdit"
                    type="button"
                    variant="outline"
                    @click="emit('cancel')"
                    class="gap-2"
                >
                    <X class="h-4 w-4" /> Cancelar Lote
                </Button>

                <Button variant="outline" v-else as-child>
                    <Link :href="itemsRoutes.index.url(teamSlug)" class="gap-2">
                        <X class="h-4 w-4" /> Cancelar
                    </Link>
                </Button>
                <Button
                    variant="default"
                    type="submit"
                    :disabled="form.processing"
                    class="gap-2"
                >
                    <component :is="isEdit ? Save : Plus" class="h-4 w-4" />
                    {{
                        form.processing
                            ? 'Salvando...'
                            : isBulkEdit
                              ? 'Atualizar Selecionados'
                              : isSingleEdit
                                ? 'Atualizar Item'
                                : 'Salvar Item'
                    }}
                </Button>
            </div>
        </form>
    </div>
</template>
