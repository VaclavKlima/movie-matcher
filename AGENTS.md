# Project Rules

- Prefer full Livewire components (class + Blade) over Volt components.
- Alpine.js is bundled with Livewire; do not import Alpine separately.
- Move complicated JavaScript out of Blade files into helper .js files.
- In Livewire, use `$this->redirectRoute(...)` and then `return;` after redirects.

## Design System & Guidelines

### Theme Overview
MovieMatcher uses a **Cinema/Movie Theater** aesthetic with playful animations and cinematic elements. All UI components should follow this theme to maintain visual consistency across the application.

### Color Palette

#### Background Colors
- **Primary backgrounds**: Deep purple/indigo gradients (`from-indigo-950 via-purple-900 to-slate-900`)
- **Card backgrounds**: Slate with transparency (`from-slate-800/95 to-slate-900/95`)
- **Overlay backgrounds**: Slate-950 with 90% opacity and backdrop blur

#### Accent Colors
- **Primary (Gold/Amber)**: Use for primary actions, highlights, and success states
  - Borders: `border-amber-400/50`
  - Backgrounds: `bg-gradient-to-r from-amber-500/30 to-amber-600/30`
  - Text: `text-amber-100`, `text-amber-300`
  - Shadows: `shadow-amber-500/30`

- **Secondary (Emerald Green)**: Use for success states, positive actions
  - `border-emerald-400/50`, `bg-emerald-500/20`, `text-emerald-300`

- **Info (Purple)**: Use for informational messages and cinema-themed elements
  - `border-purple-400/50`, `bg-purple-500/30`, `text-purple-200`

- **Warning/Danger (Rose Red)**: Use for destructive actions and errors
  - `border-rose-400/50`, `bg-rose-500/30`, `text-rose-100`

### Cinema-Themed Elements

#### Decorative Elements
- **Film Reels**: Spinning circular decorations with `animate-film-reel`
- **Spotlights**: Sweeping gradient overlays with `animate-spotlight`
- **Marquee Lights**: Pulsing border effects with `animate-marquee-lights`
- **Red Curtains**: Dramatic reveal animations for modal openings

#### Component Styling
- **Film Strip Borders**: Use `.film-strip-border` class for image containers
- **Ticket Stub**: Use `.ticket-stub` class for genre tags and labels (perforated edge effect)
- **Cinema Seats**: Use `.cinema-seat` class for participant avatars (curved seat shape)

#### Icons & Emojis
Use cinema-themed emojis consistently:
- 🎬 - General cinema/movie actions
- 🎭 - Theater/lobby contexts
- 🎫 - Tickets and entry
- 🍿 - Snacks and enjoyment
- ⭐ - Ratings and favorites
- 🎯 - Matches and targets
- 🎉 - Celebrations and success
- 👍 - Like/approve (voting positive)
- 👎 - Dislike/reject (voting negative)
- 👑 - Host/leader role
- ✅ - Success notification
- ⚠️ - Warning notification
- ❌ - Error notification

### Content Writing & Messaging Guidelines

#### Copy Voice & Tone
- **Playful & Enthusiastic**: Keep the tone light, fun, and engaging
- **Cinema-Focused**: Use movie theater and cinema terminology throughout
- **Concise**: Short, punchy sentences that are easy to scan
- **Friendly**: Avoid technical jargon; speak to users in everyday language
- **Action-Oriented**: Use active voice and clear calls-to-action

#### Cinema Terminology Mapping
Always use cinema-themed alternatives to generic terms:
- **Room** → "Screening" (e.g., "Host a Screening", "Join a Screening")
- **Lobby/Waiting Area** → "Theater Lobby"
- **Ready Status** → "Doors Open"
- **Matching Phase** → "Now Showing"
- **Continue Matching** → "Find Another Gem"
- **Match Found** → "Everyone Said YES!" or "It's a Match!"
- **Participants** → "In The Theater" or "Viewers"
- **Start Session** → "Roll Credits" or "Start the Show"

#### Button & Action Text
Use these standardized button labels:

**Voting Actions:**
- Reject: "👎 Pass"
- Accept: "👍 Watch It!"

**Navigation & Flow:**
- Continue matching: "🎬 Find Another Gem"
- Create room: "🎬 Create Room" or "🎫 Host a Screening"
- Join room: "Join Screening" or "🎭 Join a Screening"
- Enter room: "🎫 Get Your Ticket"
- Start matching: "🎬 Roll Credits" or "🍿 Start the Show"
- Finalize pick: "🎯 End with this pick"
- Disband room: "🎭 End Screening"

**Form Actions:**
- Submit: "Continue" or "Next"
- Cancel: "Cancel" or "Go Back"
- Confirm: "Confirm" or action-specific text

#### Status & Informational Messages

**Loading States:**
- "The lobby is sealed. Let the matching magic begin!"
- "Preparing the screening room..."
- "Loading your next pick..."

**Empty States:**
- "No matches yet—keep swiping!"
- "The theater is empty. Share the room code!"
- "🍿 No screenings in progress"

**Success Messages:**
- "Everyone Said YES! 🎉"
- "It's a Match!"
- "The crowd has spoken. Time to grab the popcorn and dim the lights!"

**Informational Messages:**
- "🎬 Continuing the hunt! Time to find another gem..."
- "Share the code so everyone can grab their seats before showtime!"
- "🍿 Create a room, invite friends, and swipe to a shared pick without the endless group chat debate."

**Error Messages (keep cinema theme):**
- "⚠️ Please select an avatar to get your ticket!"
- "⚠️ This screening room doesn't exist"
- "The theater is full"
- "This show has already started"

#### Section Headers & Titles
Use emojis as visual prefixes and cinema terminology:
- "🎬 Match Movies In Minutes"
- "🎬 NOW SHOWING"
- "🎭 The Theater Lobby"
- "🎫 Get Your Ticket"
- "🎬 In Theater" or "🎭 In The Theater"
- "🎫 Room Code"
- "🎬 Room [CODE]"
- "🎯 Matched Movies"

#### User Role Labels
Consistent labeling for different user types:
- **Host**: "👑 Host"
- **Regular Participant**: "🎬 Viewer" or "🎫 Viewer"
- **Current User (in lists)**: "🎫 You"
- **Ready Status**: Green dot + "Ready" or "Doors Open"

#### Descriptive Text Patterns
Use these cinema-themed phrases for descriptive content:

**Calls to Action:**
- "Grab the popcorn"
- "Dim the lights"
- "Time for showtime"
- "Roll the credits"
- "Find another gem"

**Status Descriptions:**
- "The crowd has spoken"
- "A masterpiece awaits"
- "The show must go on"
- "Screening in progress"

**Instructional Text:**
- "Share the code so everyone can grab their seats before showtime"
- "Create a room, invite friends, and swipe to a shared pick"
- "Everyone votes, and the first unanimous 'yes' wins"

#### Character Limits & Formatting
- **Button text**: Max 20 characters (including emoji)
- **Toast messages**: Max 60 characters for mobile readability
- **Page titles**: Keep under 40 characters
- **Descriptions**: 2-3 sentences maximum
- **Always include emojis** in headers and primary actions for visual interest

#### Examples from the Application

**Good Examples:**
- ✅ "🎬 Find Another Gem" (clear, playful, cinema-themed)
- ✅ "Everyone Said YES! 🎉" (exciting, celebratory)
- ✅ "👍 Watch It!" (clear action, friendly)
- ✅ "The crowd has spoken. Time to grab the popcorn and dim the lights!" (playful, cinema-themed)

**Avoid:**
- ❌ "Continue" (too generic)
- ❌ "Submit Vote" (too technical)
- ❌ "Click here to proceed" (boring, not cinema-themed)
- ❌ "User has requested to continue matching" (too formal)

### Typography

#### Headings
- Use bold/black weights: `font-bold`, `font-black`
- Apply gradient text for impact: `text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-100 to-amber-200`
- Add glow effects: `drop-shadow-[0_0_30px_rgba(251,191,36,0.3)]`
- Responsive sizing: `text-2xl sm:text-4xl md:text-5xl`

#### Labels & Badges
- Use small caps: `uppercase`
- Add letter-spacing: `tracking-[0.2em] sm:tracking-[0.3em]`
- Smaller font sizes: `text-[0.65rem] sm:text-xs`

#### Body Text
- Use lighter shades: `text-purple-200/90`, `text-slate-300`
- Maintain readability: `leading-relaxed`
- Responsive sizing: `text-sm sm:text-base`

### Animations

#### Custom Keyframes (defined in `resources/css/app.css`)
```css
@keyframes film-reel-spin { /* 360° rotation */ }
@keyframes spotlight-sweep { /* Sweeping light effect */ }
@keyframes marquee-lights { /* Pulsing opacity */ }
@keyframes curtain-open { /* Scale curtains to sides */ }
@keyframes confetti-burst { /* Celebration particles */ }
@keyframes float-gentle { /* Subtle floating */ }
@keyframes card-slide-in { /* Card entrance */ }
@keyframes pulse-glow { /* Pulsing glow effect */ }
```

#### Animation Timing
- **Major animations**: 800ms (curtain opening, card transitions)
- **Standard transitions**: 300-500ms (hover states, fades)
- **Quick interactions**: 200ms (button presses)
- **Delayed entrances**: Use `delay-[Xms]` classes or `style="animation-delay: Xs"`

#### JavaScript Animations
- Use `requestAnimationFrame` for complex, multi-step animations
- Use `setTimeout` for sequenced timing
- Example pattern:
  ```javascript
  requestAnimationFrame(() => {
      // Step 1: Initialize
      setTimeout(() => {
          // Step 2: First animation
          setTimeout(() => {
              // Step 3: Second animation
          }, 800);
      }, 200);
  });
  ```

### Layout Principles

#### Preventing Layout Shift
- **Fixed heights**: Use `h-48`, `h-64`, `h-72` for images/posters
- **Minimum heights**: Use `min-h-[5.5rem]` for text containers with dynamic content
- **Aspect ratios**: Maintain consistent aspect ratios for media

#### Responsive Design (Mobile-First)
- **Padding**: `p-4 sm:p-6 md:p-8`
- **Spacing**: `mt-4 sm:mt-6 md:mt-8`
- **Text sizes**: `text-sm sm:text-base md:text-lg`
- **Button widths**: `w-full sm:w-auto` (full width on mobile)

#### Overflow Handling
- **Modals**: `max-h-[calc(100vh-2rem)] overflow-y-auto`
- **Cards**: `overflow-hidden` with `rounded-xl sm:rounded-2xl`
- **Long text**: Use `line-clamp-3` or similar

### Component Patterns

#### Glass-morphism Cards
```html
<div class="rounded-2xl border-2 border-amber-400/50 bg-gradient-to-br from-slate-800/95 to-slate-900/95 backdrop-blur-xl shadow-2xl shadow-amber-500/30">
```

#### Hover States
- **Scale transform**: `hover:scale-105 active:scale-95`
- **Border enhancement**: `hover:border-amber-400`
- **Background intensity**: `hover:from-amber-500/40 hover:to-amber-600/40`

#### Buttons
- Use rounded corners: `rounded-xl sm:rounded-2xl`
- Include transitions: `transition-all duration-300`
- Add shadows: `shadow-2xl shadow-amber-500/30`
- Provide feedback: `hover:scale-105 active:scale-95`

#### Toast Notifications
- Position: Fixed at top center
- Theme-based colors: emerald (success), amber (warning), rose (error), purple (info)
- Icons: ✅ ⚠️ ❌ 🎬
- Auto-dismiss: 3 seconds
- Glowing accent bar on left edge

### Performance Considerations

#### Polling
- Use `wire:poll.2s.visible` for real-time updates
- Optimize queries: Select only needed columns
- Add `.visible` modifier to pause polling when tab is inactive

#### Animations
- Use CSS transforms (faster than position changes)
- Prefer `opacity` and `transform` over `width`/`height`
- Use `pointer-events-none` for decorative overlays

#### Images
- Use `object-contain` or `object-cover` appropriately
- Add `loading="lazy"` for below-the-fold images
- Maintain fixed aspect ratios

### Accessibility

- Always include `aria-modal="true"` and `role="dialog"` on modals
- Use semantic HTML (`<button>`, `<section>`, etc.)
- Maintain sufficient color contrast
- Ensure keyboard navigation works
- Include descriptive `alt` text for images

### Consistency Rules

1. **Always use the cinema theme** - No generic Bootstrap or Material Design patterns
2. **Maintain color consistency** - Use the defined color palette
3. **Follow responsive patterns** - Mobile-first with consistent breakpoints (sm:, md:, lg:)
4. **Prevent layout shift** - Use fixed heights and proper spacing
5. **Add playful animations** - But keep them performant and purposeful
6. **Test on mobile** - Ensure overflow is handled and touch targets are adequate

## Commit Message Prompt
Use this prompt with your AI assistant
```
Generate a git commit message in Conventional Commits format.
Rules:
- Format: "<type>(optional-scope): <short summary>"
- Allowed types: feat, fix, perf, refactor, docs, test, chore, ci, build
- Summary: imperative mood, <= 72 chars, no trailing period.
- If there is a breaking change, add "BREAKING CHANGE: <detail>" in the body.
- Provide ONLY the commit message, no extra text.

Context:
<PASTE THE CHANGE SUMMARY HERE>
```
