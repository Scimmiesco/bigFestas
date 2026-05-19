<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import { Save, X, Plus } from 'lucide-vue-next';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
    <div
        class="mt-6 rounded-xl border border-sidebar-border bg-white p-6 shadow-sm dark:bg-sidebar"
    >
        <div
            v-if="isBulkEdit"
            class="mb-6 rounded-md bg-blue-50 p-4 text-sm text-blue-700 dark:bg-blue-900/20 dark:text-blue-300"
        >
            <strong>Modo de Edição em Lote:</strong> Preencha apenas os campos
            que deseja alterar para todos os {{ form.ids.length }} itens
            selecionados. Deixe em branco os campos que devem manter seus
            valores originais.
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                <div
                    class="grid gap-2"
                    :class="isEdit ? 'md:col-span-12' : 'md:col-span-9'"
                >
                    <Label for="nome">
                        Nome do Item
                        <span v-if="!isBulkEdit" class="text-red-500">*</span>
                    </Label>
                    <Input
                        id="nome"
                        v-model="form.nome"
                        :placeholder="
                            isBulkEdit
                                ? 'Deixe em branco para manter o original'
                                : 'Ex: Cadeira Plástica Branca'
                        "
                        :required="!isBulkEdit"
                        :autofocus="!isBulkEdit"
                    />
                    <InputError :message="form.errors.nome" />
                </div>

                <div v-if="!isEdit" class="grid gap-2 md:col-span-3">
                    <Label for="quantidade">Quantidade</Label>
                    <Input
                        id="quantidade"
                        type="number"
                        min="1"
                        max="200"
                        v-model="form.quantidade"
                        required
                    />
                    <InputError :message="form.errors.quantidade" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="tipo">
                        Tipo
                        <span v-if="!isBulkEdit" class="text-red-500">*</span>
                    </Label>
                    <select
                        id="tipo"
                        v-model="form.tipo"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
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
                    <InputError :message="form.errors.tipo" />
                </div>

                <div class="grid gap-2">
                    <Label for="status">
                        Status
                        <span v-if="!isBulkEdit" class="text-red-500">*</span>
                    </Label>
                    <select
                        id="status"
                        v-model="form.status"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
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
                    <InputError :message="form.errors.status" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="observacoes">Observações (Opcional)</Label>
                <textarea
                    id="observacoes"
                    v-model="form.observacoes"
                    rows="3"
                    class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                    :placeholder="
                        isBulkEdit
                            ? 'Deixe em branco para manter original'
                            : 'Detalhes adicionais...'
                    "
                ></textarea>
                <InputError :message="form.errors.observacoes" />
            </div>

            <div
                class="mt-6 flex items-center justify-end gap-4 border-t pt-6 dark:border-sidebar-border"
            >
                <Button
                    v-if="isBulkEdit"
                    type="button"
                    variant="outline"
                    @click="emit('cancel')"
                    class="flex items-center gap-2"
                >
                    <X class="h-4 w-4" /> Cancelar Lote
                </Button>
                <Button v-else type="button" variant="outline" as-child>
                    <Link
                        :href="itemsRoutes.index.url(teamSlug)"
                        class="flex items-center gap-2"
                    >
                        <X class="h-4 w-4" /> Cancelar
                    </Link>
                </Button>

                <Button
                    type="submit"
                    :disabled="form.processing"
                    class="flex items-center gap-2"
                >
                    <component :is="isEdit ? Save : Plus" class="h-4 w-4" />
                    {{
                        form.processing
                            ? 'Salvando...'
                            : isBulkEdit
                              ? 'Atualizar Itens Selecionados'
                              : isSingleEdit
                                ? 'Atualizar Item'
                                : 'Salvar Item'
                    }}
                </Button>
            </div>
        </form>
    </div>
</template>
