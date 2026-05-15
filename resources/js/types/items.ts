import type { ItemStatus } from './enum';

export interface Item {
    id: number;
    status: ItemStatus;
}
