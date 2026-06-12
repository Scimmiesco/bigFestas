<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import { Save, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import addressesRoutes from '@/routes/addresses/index';

const props = defineProps<{
    endereco?: any;
    clients?: any;
    enums?: any;
    teamSlug: string;
}>();

const emit = defineEmits(['cancel', 'success']);

const form = useForm({
    id: props.endereco?.id ?? 0,
    client_id: props.endereco?.client_id ?? '',
    tipo_endereco: props.endereco?.tipo_endereco ?? '',
    cep: props.endereco?.cep ?? '',
    logradouro: props.endereco?.logradouro ?? '',
    numero: props.endereco?.numero ?? '',
    complemento: props.endereco?.complemento ?? '',
    bairro: props.endereco?.bairro ?? '',
    cidade: props.endereco?.cidade ?? '',
    uf: props.endereco?.uf ?? '',
});

const submit = () => {
    form.post(`/${props.teamSlug}/enderecos`, {
        preserveScroll: true,
        onSuccess: () => emit('success'),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="form-wrapper">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="client_id" class="form-label">
                    Cliente
                    <span class="form-required-asterisk">*</span>
                </label>
                <select
                    id="client_id"
                    v-model="form.client_id"
                    class="form-input"
                    :required="true"
                >
                    <option :disabled="true" :selected="true" value="">
                        Selecione um cliente
                    </option>
                    <option
                        v-for="client in clients"
                        :key="client.id"
                        :value="client.id"
                    >
                        {{ client.nome }}
                    </option>
                </select>
                <InputError :message="form.errors.client_id" />
            </div>

            <div>
                <label for="tipo_endereco" class="form-label">
                    Tipo de Endereço
                    <span class="form-required-asterisk">*</span>
                </label>
                <select
                    id="tipo_endereco"
                    v-model="form.tipo_endereco"
                    class="form-input"
                    :required="true"
                >
                    <option :disabled="true" :selected="true" value="">
                        Selecione um tipo
                    </option>
                    <option
                        v-for="tipo in enums?.address_types"
                        :key="tipo.value"
                        :value="tipo.value"
                    >
                        {{ tipo.label }}
                    </option>
                </select>
                <InputError :message="form.errors.tipo_endereco" />
            </div>

            <div>
                <label for="cep" class="form-label">
                    CEP
                    <span class="form-required-asterisk">*</span>
                </label>
                <input
                    id="cep"
                    type="text"
                    class="form-input"
                    v-model="form.cep"
                    placeholder="00000-000"
                    :required="true"
                />
                <InputError :message="form.errors.cep" />
            </div>

            <div class="md:col-span-2">
                <label for="logradouro" class="form-label">
                    Logradouro
                    <span class="form-required-asterisk">*</span>
                </label>
                <input
                    id="logradouro"
                    type="text"
                    class="form-input"
                    v-model="form.logradouro"
                    placeholder="Rua, Avenida, etc."
                    :required="true"
                />
                <InputError :message="form.errors.logradouro" />
            </div>

            <div>
                <label for="numero" class="form-label">
                    Número
                    <span class="form-required-asterisk">*</span>
                </label>
                <input
                    id="numero"
                    type="text"
                    class="form-input"
                    v-model="form.numero"
                    placeholder="123 ou S/N"
                    :required="true"
                />
                <InputError :message="form.errors.numero" />
            </div>

            <div>
                <label for="complemento" class="form-label">Complemento</label>
                <input
                    id="complemento"
                    type="text"
                    class="form-input"
                    v-model="form.complemento"
                    placeholder="Apto 12, Bloco B"
                />
                <InputError :message="form.errors.complemento" />
            </div>

            <div>
                <label for="bairro" class="form-label">
                    Bairro
                    <span class="form-required-asterisk">*</span>
                </label>
                <input
                    id="bairro"
                    type="text"
                    class="form-input"
                    v-model="form.bairro"
                    placeholder="Centro"
                    :required="true"
                />
                <InputError :message="form.errors.bairro" />
            </div>

            <div>
                <label for="cidade" class="form-label">
                    Cidade
                    <span class="form-required-asterisk">*</span>
                </label>
                <input
                    id="cidade"
                    type="text"
                    class="form-input"
                    v-model="form.cidade"
                    placeholder="São Paulo"
                    :required="true"
                />
                <InputError :message="form.errors.cidade" />
            </div>

            <div>
                <label for="uf" class="form-label">
                    Estado (UF)
                    <span class="form-required-asterisk">*</span>
                </label>
                <input
                    id="uf"
                    type="text"
                    class="form-input"
                    v-model="form.uf"
                    placeholder="SP"
                    maxlength="2"
                    :required="true"
                />
                <InputError :message="form.errors.uf" />
            </div>
        </div>

        <div class="form-actions mt-6">
            <Button variant="outline" as-child>
                <Link :href="addressesRoutes.index(teamSlug)" class="gap-2">
                    <X class="h-4 w-4" /> Cancelar
                </Link>
            </Button>
            <Button
                variant="default"
                type="submit"
                :disabled="form.processing"
                class="gap-2"
            >
                <Save class="h-4 w-4" />
                {{ form.processing ? 'Salvando...' : 'Salvar Endereço' }}
            </Button>
        </div>
    </form>
</template>
