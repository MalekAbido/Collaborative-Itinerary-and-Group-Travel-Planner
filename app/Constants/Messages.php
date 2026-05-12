<?php

namespace App\Constants;

class Messages
{
    // Auth & Access
    public const AUTH_REQUIRED = 'Please log in to continue.';
    public const ACCESS_DENIED = 'Access denied. You do not have permission to perform this action.';
    public const NOT_A_MEMBER = 'You are not a member of this itinerary.';
    public const ALREADY_LOGGED_IN = 'You are already logged in!';
    public const LOGIN_SUCCESS = 'Welcome back!';
    public const LOGOUT_SUCCESS = 'You have been logged out.';
    public const REGISTER_SUCCESS = 'Registration successful! Please log in.';
    public const PROFILE_UPDATED = 'Profile updated successfully.';

    // Generic Success/Error
    public const SUCCESS_GENERIC = 'Action completed successfully.';
    public const ERROR_GENERIC = 'An error occurred. Please try again.';
    public const ERROR_NOT_FOUND = 'The requested item was not found.';
    public const DATABASE_ERROR = 'A database error occurred. Please try again later.';

    // Itinerary
    public const ITIN_CREATED = 'Itinerary created successfully.';
    public const ITIN_UPDATED = 'Itinerary updated successfully.';
    public const ITIN_DELETED = 'Itinerary deleted successfully.';
    public const ITIN_DATE_ERROR = 'The end date cannot be before the start date.';
    public const ITIN_MEMBER_ACCESS = 'You must be a member of this itinerary to access this.';
    public const MEMBER_LEFT = 'You have left the itinerary.';
    public const MEMBER_INVITED = 'Invitation sent successfully.';
    public const ROLE_UPDATED = 'Member role updated successfully.';
    public const MEMBER_REMOVED = 'Member removed from itinerary.';

    // Activity
    public const ACTIVITY_REQUIRED_FIELDS = 'Name, start time, and end time are required.';
    public const ACTIVITY_TIME_ERROR = 'End time must be after start time.';
    public const ACTIVITY_DURATION_ERROR = 'Activity duration cannot exceed 24 hours.';
    public const ACTIVITY_BOUND_ERROR = 'Activity must be within the trip dates.';
    public const ACTIVITY_CREATED = 'Activity created successfully.';
    public const ACTIVITY_CREATED_DRAFT = 'Activity created successfully as a Draft.';
    public const ACTIVITY_REMOVED = 'Activity removed successfully.';
    public const ACTIVITY_NOT_FOUND = 'Activity not found or does not belong to this trip.';
    public const ATTENDANCE_UPDATED = 'Your attendance status has been updated.';
    public const ATTENDANCE_ERROR = 'Attendance tracking is not set up for this activity.';

    // Polls
    public const POLL_CREATED = 'Poll created successfully.';
    public const POLL_CLOSED = 'Poll has been closed.';
    public const POLL_REOPENED = 'Poll has been reopened with a new deadline.';
    public const POLL_DEADLINE_ERROR = 'Poll deadline must be at least 24 hours before the activity starts.';
    public const VOTE_SUCCESS = 'Your vote has been cast!';
    public const VOTE_UPDATED = 'Your vote has been updated!';

    // Proposal
    public const PROPOSAL_APPROVED = 'Proposal approved successfully.';
    public const PROPOSAL_REJECTED = 'Proposal has been rejected.';

    // Finance & Expenses
    public const EXPENSE_ADDED = 'Expense added successfully.';
    public const EXPENSE_DELETED = 'Expense deleted successfully.';
    public const FINANCE_ACCESS_DENIED = 'You must be a member of this itinerary to view its finances.';
    public const SETTLEMENT_PAID = 'Settlement marked as paid.';
    public const CONTRIBUTION_ADDED = 'Contribution added successfully.';
    public const CONTRIBUTION_DELETED = 'Contribution deleted successfully.';
    public const BUDGET_UPDATED = 'Budget limit updated successfully.';
    public const GROUP_FUND_CREATED = 'Group fund created successfully.';

    // Inventory
    public const INVENTORY_REMOVED = 'Item removed from inventory.';
    public const INVENTORY_VOLUNTEERED = 'You have volunteered to bring this item.';
    public const INVENTORY_UNVOLUNTEERED = 'You are no longer bringing this item.';
    public const INVENTORY_ACCESS_DENIED = 'You do not have permission to remove this item.';
    public const INV_FUTURE_CONFIRMED_ONLY = 'Inventory items can only be linked to future confirmed activities.';

    // Emergency & Medical
    public const EMERGENCY_CONTACT_ADDED = 'Emergency contact added successfully.';
    public const EMERGENCY_CONTACT_REMOVED = 'Emergency contact removed successfully.';
    public const EMERGENCY_CONTACT_UPDATED = 'Emergency contact updated successfully.';
    public const ALLERGY_ADDED = 'Medical record added successfully.';
    public const ALLERGY_REMOVED = 'Medical record removed successfully.';
    public const ALLERGY_UPDATED = 'Medical record updated successfully.';

    // History & Rollback
    public const HISTORY_NOT_FOUND = 'Log entry not found.';
    public const HISTORY_UNDO_ERROR = 'Only the most recent action for this entity can be undone.';
    public const REVERT_SUCCESS = 'Action reverted successfully.';
    public const REVERT_ERROR = 'Failed to revert the action.';
}
