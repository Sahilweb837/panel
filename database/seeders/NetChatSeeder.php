<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\ChatChannel;
use App\Models\ChatChannelMember;
use App\Models\ChatMessage;

class NetChatSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure staff/student/admin roles exist
        $adminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['role_name' => 'Super Admin', 'status' => 1]);
        $staffRole = Role::firstOrCreate(['slug' => 'staff'], ['role_name' => 'Staff', 'status' => 1]);

        // 2. Team users from the Slack Conceptzilla photo
        $team = [
            [
                'name' => 'Andrew Miller',
                'short' => 'Andrew M.',
                'email' => 'andrew.m@conceptzilla.com',
                'username' => 'andrew.m',
                'designation' => 'Product owner',
                'badge' => 'Management',
                'avatar' => null,
            ],
            [
                'name' => 'Diana Taylor',
                'short' => 'Diana T.',
                'email' => 'diana.t@conceptzilla.com',
                'username' => 'diana.t',
                'designation' => 'Lead UI Designer',
                'badge' => 'Design',
                'avatar' => null,
            ],
            [
                'name' => 'Daniel Anderson',
                'short' => 'Daniel A.',
                'email' => 'daniel.a@conceptzilla.com',
                'username' => 'daniel.a',
                'designation' => 'Art director',
                'badge' => 'Design',
                'avatar' => null,
            ],
            [
                'name' => 'Sophia Wilson',
                'short' => 'Sophia Wilson',
                'email' => 'sophia.w@conceptzilla.com',
                'username' => 'sophia.w',
                'designation' => 'Senior Product Designer',
                'badge' => 'Design',
                'avatar' => null,
            ],
            [
                'name' => 'William Johnson',
                'short' => 'William Johnson',
                'email' => 'william.j@conceptzilla.com',
                'username' => 'william.j',
                'designation' => 'UX/UI designer',
                'badge' => 'Design',
                'avatar' => null,
            ],
            [
                'name' => 'Emily Davis',
                'short' => 'Emily D.',
                'email' => 'emily.d@conceptzilla.com',
                'username' => 'emily.d',
                'designation' => 'Front-end dev',
                'badge' => 'Development',
                'avatar' => null,
            ],
        ];

        $createdUsers = [];
        foreach ($team as $m) {
            $user = User::firstOrCreate(
                ['email' => $m['email']],
                [
                    'name' => $m['name'],
                    'username' => $m['username'],
                    'password' => Hash::make('password123'),
                    'role_id' => $staffRole->id,
                    'status' => 1,
                ]
            );
            $createdUsers[$m['short']] = [
                'user' => $user,
                'designation' => $m['designation'],
                'badge' => $m['badge'],
            ];
        }

        // Get Super Admin as well
        $superAdmin = User::where('role_id', $adminRole->id)->first();
        if (!$superAdmin) {
            $superAdmin = User::first();
        }

        // 3. Create Channels Hierarchy
        // Parent: Website
        $website = ChatChannel::firstOrCreate(
            ['slug' => 'website'],
            [
                'name' => 'Website',
                'icon' => '#',
                'type' => 'category',
                'topic' => 'Main website projects and initiatives',
                'creator_id' => $createdUsers['Andrew M.']['user']->id ?? 1,
                'is_favorite' => false,
                'order_index' => 3,
                'status_badge' => 'Active',
            ]
        );

        // Subcategory: v3.0
        $v3 = ChatChannel::firstOrCreate(
            ['slug' => 'website-v3-0'],
            [
                'name' => 'v3.0',
                'parent_id' => $website->id,
                'icon' => '↳',
                'type' => 'subchannel',
                'topic' => 'Version 3.0 redesign sprint',
                'creator_id' => $createdUsers['Andrew M.']['user']->id ?? 1,
                'order_index' => 1,
                'status_badge' => 'Active',
            ]
        );

        // Subchannels of v3.0
        $wireframe = ChatChannel::firstOrCreate(
            ['slug' => 'website-v3-wireframe'],
            [
                'name' => 'Wireframe',
                'parent_id' => $v3->id,
                'icon' => '↳',
                'type' => 'channel',
                'topic' => 'Wireframes and initial architecture drafts',
                'order_index' => 1,
                'status_badge' => 'Active',
            ]
        );

        $design = ChatChannel::firstOrCreate(
            ['slug' => 'website-v3-design'],
            [
                'name' => 'Design',
                'parent_id' => $v3->id,
                'icon' => '↳',
                'type' => 'channel',
                'topic' => 'High-fidelity Figma components & prototypes',
                'order_index' => 2,
                'status_badge' => 'Active',
            ]
        );

        // The exact active channel from photo: UI-kit design
        $uiKitDesign = ChatChannel::firstOrCreate(
            ['slug' => 'website-v3-ui-kit-design'],
            [
                'name' => 'UI-kit design',
                'parent_id' => $v3->id,
                'icon' => '↳',
                'type' => 'channel',
                'topic' => 'Shared design tokens, custom components, typography & state styles',
                'description' => 'Official design system channel for Website v3.0 UI-kit tokens, button styles, states, and guidelines.',
                'creator_id' => $createdUsers['Andrew M.']['user']->id ?? 1,
                'is_favorite' => false,
                'order_index' => 3,
                'tasks_count' => 4,
                'tags_count' => 13,
                'status_badge' => 'Active',
            ]
        );

        // Subchannel v2.0
        ChatChannel::firstOrCreate(
            ['slug' => 'website-v2-0-actual'],
            [
                'name' => 'v2.0 - actual version',
                'parent_id' => $website->id,
                'icon' => '↳',
                'type' => 'channel',
                'topic' => 'Production site maintenance',
                'order_index' => 2,
            ]
        );

        // Other top channels
        ChatChannel::firstOrCreate(
            ['slug' => 'general'],
            [
                'name' => 'General',
                'icon' => '🔥',
                'topic' => 'Company-wide updates and casual watercooler chat',
                'order_index' => 1,
            ]
        );

        ChatChannel::firstOrCreate(
            ['slug' => 'front-end'],
            [
                'name' => 'front-end',
                'icon' => '#',
                'topic' => 'Frontend web engineering, React/Vue/Blade & CSS',
                'is_favorite' => true,
                'order_index' => 2,
            ]
        );

        ChatChannel::firstOrCreate(
            ['slug' => 'strategy'],
            [
                'name' => 'Strategy',
                'icon' => '🎯',
                'topic' => 'Quarterly planning, OKRs & roadmap discussions',
                'order_index' => 4,
            ]
        );

        ChatChannel::firstOrCreate(
            ['slug' => 'events'],
            [
                'name' => 'Events',
                'icon' => '📍',
                'topic' => 'Team meetups, workshops, hackathons & demos',
                'order_index' => 5,
            ]
        );

        ChatChannel::firstOrCreate(
            ['slug' => 'announcements'],
            [
                'name' => 'Announcements',
                'icon' => '📢',
                'topic' => 'Official broadcasts, policy releases & notices',
                'order_index' => 6,
            ]
        );

        ChatChannel::firstOrCreate(
            ['slug' => 'ui-ux'],
            [
                'name' => 'UI/UX',
                'icon' => '#',
                'topic' => 'General design feedback, crits & inspirations',
                'order_index' => 7,
            ]
        );

        // 4. Attach members to $uiKitDesign channel
        foreach ($createdUsers as $info) {
            ChatChannelMember::firstOrCreate(
                [
                    'channel_id' => $uiKitDesign->id,
                    'user_id' => $info['user']->id,
                ],
                [
                    'role_badge' => $info['badge'],
                    'designation' => $info['designation'],
                    'is_admin' => $info['badge'] === 'Management',
                ]
            );
        }

        if ($superAdmin) {
            ChatChannelMember::firstOrCreate(
                [
                    'channel_id' => $uiKitDesign->id,
                    'user_id' => $superAdmin->id,
                ],
                [
                    'role_badge' => 'Management',
                    'designation' => 'System Director',
                    'is_admin' => true,
                ]
            );
        }

        // 5. Seed the exact chat messages shown in the screenshot for $uiKitDesign
        if (ChatMessage::where('channel_id', $uiKitDesign->id)->count() === 0) {
            $andrew = $createdUsers['Andrew M.']['user'];
            $diana = $createdUsers['Diana T.']['user'];
            $daniel = $createdUsers['Daniel A.']['user'];

            // Message 1 (Andrew M.)
            ChatMessage::create([
                'channel_id' => $uiKitDesign->id,
                'sender_id' => $andrew->id,
                'message' => "Hey team, I wanted to discuss the custom UI-kit we're developing for the site redesign. We need to finalize some components and make key design decisions to ensure consistency across the board. Let's make sure we cover colors, typography, buttons, and any other essential UI elements.",
                'mentions' => ['@UX/UI', '@Sophia'],
                'reactions' => ['👍' => [$andrew->id, $daniel->id], '😊' => [$diana->id]],
                'created_at' => now()->subDays(2),
            ]);

            // Message 2 (Diana T.)
            ChatMessage::create([
                'channel_id' => $uiKitDesign->id,
                'sender_id' => $diana->id,
                'message' => "I have already prepared all styles and components according to our standards during the design phase, so the UI kit is 90% complete. All that remains is to add some states to the interactive elements and prepare the Lottie files for animations.\n\n@Emily D., please take a look and let me know if you have any questions.",
                'mentions' => ['@Emily D.'],
                'link_preview' => [
                    'title' => 'Conceptzilla website v3.0',
                    'subtitle' => 'www.figma.com',
                    'url' => 'https://www.figma.com',
                    'icon' => 'figma',
                    'button_text' => 'Quick view',
                ],
                'reactions' => ['❤️' => [$andrew->id], '😊' => [$daniel->id]],
                'created_at' => now()->subDays(2)->addHours(3),
            ]);

            // Message 3 (Daniel A.)
            ChatMessage::create([
                'channel_id' => $uiKitDesign->id,
                'sender_id' => $daniel->id,
                'message' => "Okay, keep me updated. @Diana T. I also wanted to remind you to keep the layers organized.",
                'mentions' => ['@Diana T.'],
                'reactions' => ['👍' => [$diana->id]],
                'created_at' => now()->subHours(3),
            ]);
        }
    }
}
