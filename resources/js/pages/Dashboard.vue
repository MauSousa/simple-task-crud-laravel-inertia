<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, WhenVisible, router, useForm } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import Button from '@/components/ui/button/Button.vue'
import TextLink from '@/components/TextLink.vue'
import { LoaderCircle } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import Toaster from '@/components/ui/toast/Toaster.vue'
import { useToast } from '@/components/ui/toast/use-toast'
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet'

const { toast } = useToast()

type Task = {
    id: string;
    name: string;
    slug: string;
    is_completed: boolean;
}

interface Props {
    tasks?: Task
    completed?: number,
    pending?: number,
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task Dashboard',
        href: '/dashboard',
    },
];

const completeTask = (taskId) => {
    const data = {
        is_completed: true
    }
    router.patch(route('tasks.update', { task: taskId }), data)

    toast({
        title: 'Success',
        description: 'Your task was completed successfully',
    });
}

const deleteTask = (taskId) => {
    router.delete(route('tasks.destroy', { task: taskId }))
    toast({
        title: 'Success',
        description: 'Your task was successfully deleted',
    });

}

const form = useForm({
    name: '',
    description: '',
})

const submit = () => {
    form.post(route('tasks.store'), {
        onFinish: () => form.reset('name', 'description'),
    });
}
</script>

<template>

    <Head title="Task dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-2">
                <Toaster />
                <div class="p-3 col-span-2">
                    <Sheet>
                        <SheetTrigger class="hover:underline hover:underline-offset-2">Add Task</SheetTrigger>
                        <SheetContent>
                            <SheetHeader>
                                <SheetTitle>You are going to create a task</SheetTitle>
                                <SheetDescription>
                                    Please complete the following form and don't forget to fill all the fields.
                                    <form @submit.prevent="submit" class="mt-3 space-y-4">
                                        <div class="grid gap-2">
                                            <Label for="name">Name of the task</Label>
                                            <Input id="name" required autofocus :tabindex="1" autocomplete="task"
                                                v-model="form.name" placeholder="Task" />
                                            <InputError :message="form.errors.name" />
                                        </div>
                                        <div class="grid gap-2">
                                            <Label for="desc">Description</Label>
                                            <Textarea id="desc" placeholder="Type description" :tabindex="2"
                                                v-model="form.description" />
                                            <InputError :message="form.errors.description" />
                                        </div>
                                        <Button type="submit" class="mt-4 w-full" :tabindex="4"
                                            :disabled="form.processing">
                                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                            Create Task
                                        </Button>
                                    </form>
                                </SheetDescription>
                            </SheetHeader>
                        </SheetContent>
                    </Sheet>
                </div>
                <div
                    class="relative w-full h-20 aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="flex justify-center items-center h-full space-x-3">
                        <p>Pending tasks</p>
                        <p>{{ pending }}</p>
                    </div>
                </div>
                <div
                    class="relative w-full h-20 aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="flex justify-center items-center h-full space-x-3">
                        <p>Completed tasks</p>
                        <p>{{ completed }}</p>
                    </div>
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <WhenVisible data="tasks" :buffer="500">
                    <template #fallback>
                        <div>Loading...</div>
                    </template>
                    <Table>
                        <TableCaption>A list of your tasks.</TableCaption>
                        <TableHeader>
                            <TableRow>
                                <TableHead>
                                    Id
                                </TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Description</TableHead>
                                <TableHead class="text-right">
                                    Action
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="(task, index) in tasks" :key="task.id">
                                <TableCell class="font-medium">
                                    {{ index += 1 }}
                                </TableCell>
                                <TableCell>{{ task.is_completed ? 'Completed' : 'Pending' }}</TableCell>
                                <TableCell>{{ task.name }}</TableCell>
                                <TableCell>{{ task.description }}</TableCell>
                                <TableCell class="flex space-x-5">
                                    <Button v-if="!task.is_completed" variant="secondary"
                                        @click="completeTask(task.id)">Complete</Button>
                                    <Button variant="destructive" @click="deleteTask(task.id)">Delete</Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </WhenVisible>
            </div>
        </div>
    </AppLayout>
</template>
